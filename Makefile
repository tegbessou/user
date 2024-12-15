DOCKER_COMPOSE = docker compose
EXEC_PHP = $(DOCKER_COMPOSE) exec -T -u www-data -e PHP_CS_FIXER_IGNORE_ENV=1 php
EXEC_YARN = $(DOCKER_COMPOSE) exec -T -u www-data php yarn --cache-folder=/home/app
EXEC_SYMFONY = $(DOCKER_COMPOSE) exec -T -u www-data php bin/console
EXEC_DB = $(DOCKER_COMPOSE) exec -T db sh -c
EXEC_MONGODB = $(DOCKER_COMPOSE) exec -T document
COMPOSER = $(EXEC_PHP) composer

.DEFAULT_GOAL := help

help: ## This help dialog.
	@echo "${GREEN}User - Des amis, du vin${RESET}"
	@awk '/^[a-zA-Z\-\_0-9]+:/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = substr($$1, 0, index($$1, ":")); helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			printf "  ${YELLOW}%-$(TARGET_MAX_CHAR_NUM)s${RESET} ${GREEN}%s${RESET}\n", helpCommand, helpMessage; \
		} \
		isTopic = match(lastLine, /^###/); \
	    if (isTopic) { printf "\n%s\n", $$1; } \
	} { lastLine = $$0 }' $(MAKEFILE_LIST)

docker-compose.override.yaml:
	@cp docker-compose.override.yaml.dist docker-compose.override.yaml

.PHONY: docker-compose.override.yaml

#################################
Docker:

pull: docker-compose.override.yaml
	@echo "\nPulling local images...\e[0m"
	@$(DOCKER_COMPOSE) pull --quiet

build: docker-compose.override.yaml pull ##Build docker
	@echo "\nBuilding local images...\e[0m"
	@$(DOCKER_COMPOSE) build

## Up environment
up: docker-compose.override.yaml ##Up docker
	@$(DOCKER_COMPOSE) up -d --remove-orphans

## Down environment
down: docker-compose.override.yaml ##Down docker
	@$(DOCKER_COMPOSE) kill
	@$(DOCKER_COMPOSE) down --remove-orphans

## View output from all containers
logs: docker-compose.override.yaml ##Logs from docker
	@${DOCKER_COMPOSE} logs -f --tail 0

.PHONY: pull build up down logs

#################################
Project:

## Up the project and load database
install: build up vendor db-load-fixtures setup-transports

## Reset the project
reset: down install

## Start containers (unpause)
start: docker-compose.override.yaml
	@$(DOCKER_COMPOSE) unpause || true
	@$(DOCKER_COMPOSE) start || true

##Stop containers (pause)
stop: docker-compose.override.yaml
	@$(DOCKER_COMPOSE) pause || true

##Install composer
vendor: composer.lock
	@echo "\nInstalling composer packages...\e[0m"
	@$(COMPOSER) install

## Update composer
composer-update: composer.json
	@echo "\nUpdating composer packages...\e[0m"
	@$(COMPOSER) update

## Clear symfony cache
cc:
	@echo "\nClearing cache...\e[0m"
	@$(EXEC_SYMFONY) c:c
	@$(EXEC_SYMFONY) cache:pool:clear cache.global_clearer

wait-db:
	@echo "\nWaiting for DB...\e[0m"
	@$(EXEC_PHP) php -r "set_time_limit(60);for(;;){if(@fsockopen('db',3306))die;echo \"\";sleep(1);}"

## Change env to test
env-test:
	@echo "Switch to ${YELLOW}test${RESET}"
	@-$(EXEC_PHP) bash -c 'grep APP_ENV= .env.local 1>/dev/null 2>&1 || echo -e "\nAPP_ENV=test" >> .env.local'
	@-$(EXEC_PHP) sed -i 's/APP_ENV=.*/APP_ENV=test/g' .env.local

## Change env to dev
env-dev:
	@echo "Switch to ${YELLOW}dev${RESET}"
	@-$(EXEC_PHP) bash -c 'grep APP_ENV= .env.local 1>/dev/null 2>&1 || echo -e "\nAPP_ENV=dev" >> .env.local'
	@-$(EXEC_PHP) sed -i 's/APP_ENV=.*/APP_ENV=dev/g' .env.local

## Setup messenger transports
setup-transports: env-test
	@echo "Setup messenger transports...\e[0m"
	@$(EXEC_SYMFONY) messenger:setup-transports

.PHONY: install reset start stop vendor composer-update cc wait-db

#################################
Database:

## Load database from dump
db-load-fixtures: wait-db db-drop db-create
	@echo "\nLoading fixtures from dump...\e[0m"
	@$(EXEC_DB) "mysql --user=root --password=root < /home/app/dump/eda_user.sql"
	@$(EXEC_MONGODB) mongorestore --drop --db eda_user /home/app/dump/documents/eda_user

## Load database from dump test
db-load-fixtures-test: wait-db db-drop-test db-create-test
	@echo "\nLoading fixtures from dump...\e[0m"
	@$(EXEC_DB) "mysql --user=root --password=root < /home/app/dump/eda_user-test.sql"
	@$(EXEC_MONGODB) mongorestore --drop --db eda_user_test /home/app/dump/documents/eda_user_test

## Recreate database structure
db-reload-schema: wait-db db-drop db-create db-migrate

## Recreate database structure test
db-reload-schema-test: wait-db db-drop-test db-create-test db-migrate-test

## Create database
db-create: wait-db
	@echo "\nCreating database...\e[0m"
	@$(EXEC_SYMFONY) doctrine:database:create --if-not-exists

## Create database test
db-create-test: env-test wait-db
	@echo "\nCreating database...\e[0m"
	@$(EXEC_SYMFONY) doctrine:database:create --if-not-exists

## Drop database
db-drop: wait-db
	@echo "\nDropping database...\e[0m"
	@$(EXEC_SYMFONY) doctrine:database:drop --force --if-exists

## Drop database test
db-drop-test: env-test wait-db
	@echo "\nDropping database...\e[0m"
	@$(EXEC_SYMFONY) doctrine:database:drop --force --if-exists

## Generate migration by diff
db-diff: wait-db
	$(EXEC_SYMFONY) doctrine:migration:diff --formatted --allow-empty-diff

## Load migration
db-migrate: env-dev wait-db
	@echo "\nRunning migrations...\e[0m"
	@$(EXEC_SYMFONY) doctrine:migration:migrate --no-interaction --all-or-nothing

## Load migration
db-migrate-test: env-test wait-db
	@echo "\nRunning migrations...\e[0m"
	@$(EXEC_SYMFONY) doctrine:migration:migrate --no-interaction --all-or-nothing --env=test

## Reload fixtures
db-reload-fixtures: env-dev wait-db db-reload-schema
	@echo "\nLoading fixtures from fixtures files...\e[0m"
	@$(EXEC_SYMFONY) doctrine:fixtures:load --no-interaction
	@$(EXEC_SYMFONY) doctrine:mongodb:fixtures:load --no-interaction

	@echo "\nCreating dump...\e[0m"
	@$(EXEC_DB) "mysqldump --user=root --password=root --databases eda_user > /home/app/dump/eda_user.sql"
	@$(EXEC_MONGODB) mongodump --db eda_user --out /home/app/dump/documents

## Reload fixtures
db-reload-fixtures-test: env-test wait-db db-reload-schema-test
	@echo "\nLoading fixtures from fixtures files...\e[0m"
	@$(EXEC_SYMFONY) doctrine:fixtures:load --no-interaction
	@$(EXEC_SYMFONY) doctrine:mongodb:fixtures:load --no-interaction

	@echo "\nCreating dump...\e[0m"
	@$(EXEC_DB) "mysqldump --user=root --password=root --databases eda_user_test > /home/app/dump/eda_user-test.sql"
	@$(EXEC_MONGODB) mongodump --db eda_user_test --out /home/app/dump/documents

#################################
Test:

## Launch unit test
unit-test: env-test
	@echo "\nLaunching unit tests\e[0m"
	@$(EXEC_PHP) bin/phpunit --testsuite unit-test --display-warnings
	@$(MAKE) env-dev

## Launch adapter test
adapter-test: env-test db-load-fixtures-test
	@echo "\nLaunching adapter tests\e[0m"
	@$(EXEC_PHP) bin/phpunit --testsuite adapter-test --display-warnings
	@$(MAKE) env-dev

## Launch feature test
feature-test: env-test db-load-fixtures-test
	@echo "\nLaunching feature tests\e[0m"
	@$(EXEC_PHP) bin/phpunit --testsuite feature-test --display-warnings
	@$(MAKE) env-dev

#################################
Quality assurance:

## Launch all quality assurance step
code-quality: security-checker yaml-linter xliff-linter twig-linter container-linter phpstan deptrac rector cs db-validate

## Security check on dependencies
security-checker:
	@echo "\nRunning security checker...\e[0m"
	@$(EXEC_PHP) sh -c "local-php-security-checker"

## Phpmd
phpmd:
	@echo "\nRunning phpmd...\e[0m"
	@$(EXEC_PHP) vendor/bin/phpmd src/ text .phpmd.xml

## Linter yaml
yaml-linter:
	@echo "\nRunning yaml linter...\e[0m"
	@$(EXEC_SYMFONY) lint:yaml src/ config/ fixtures/ docker*

## Linter xliff
xliff-linter:
	@echo "\nRunning xliff linter...\e[0m"
	@$(EXEC_SYMFONY) lint:xliff translations/

## Linter twig
twig-linter:
	@echo "\nRunning twig linter...\e[0m"
	@$(EXEC_SYMFONY) lint:twig templates/

## Container yaml
container-linter:
	@echo "\nRunning container linter...\e[0m"
	@$(EXEC_SYMFONY) lint:container

## PHPStan with higher level
phpstan:
	@echo "\nRunning phpstan...\e[0m"
	@$(EXEC_PHP) vendor/bin/phpstan analyse src/ --level 8 --memory-limit=1G

## Deptrac
deptrac:
	@echo "\nRunning deptrac for hexagonal architecture...\e[0m"
	@$(EXEC_PHP) vendor/bin/deptrac analyze --cache-file .deptrac_hexagonal_architecture.cache --config-file deptrac_hexagonal_architecture.yaml --fail-on-uncovered --report-uncovered --no-progress

## Rector
rector:
	@echo "\nRunning rector...\e[0m"
	@$(EXEC_PHP) vendor/bin/rector --clear-cache --dry-run

## Fix rector problem
rector-apply:
	@echo "\nRunning rector and fix problem...\e[0m"
	@$(EXEC_PHP) vendor/bin/rector --clear-cache

## Show cs fixer error
cs:
	@echo "\nRunning cs fixer in dry run...\e[0m"
	@$(EXEC_PHP) vendor/bin/php-cs-fixer fix --dry-run --using-cache=no --verbose --allow-risky=yes --diff --config=php-cs-fixer.dist.php

## Fix cs fixer error
cs-fix:
	@echo "\nRunning cs fixer...\e[0m"
	@$(EXEC_PHP) vendor/bin/php-cs-fixer fix --using-cache=no --verbose --allow-risky=yes --diff --config=php-cs-fixer.dist.php

## Validate db schema
db-validate:
	@echo "\nRunning db validate...\e[0m"
	@$(EXEC_SYMFONY) doctrine:schema:validate

.PHONY: eslint