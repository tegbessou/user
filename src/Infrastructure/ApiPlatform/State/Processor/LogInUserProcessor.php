<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\ApiPlatform\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use EmpireDesAmis\User\Application\Command\LogInUserServiceCommand;
use EmpireDesAmis\User\Infrastructure\ApiPlatform\Resource\AuthorizeTokenResource;
use EmpireDesAmis\User\Infrastructure\ApiPlatform\Resource\PostLoginUserResource;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidPayloadException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Monolog\Attribute\WithMonologChannel;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use TegCorp\SharedKernelBundle\Application\Command\CommandBusInterface;
use Webmozart\Assert\Assert;

/**
 * @implements ProcessorInterface<PostLoginUserResource, AuthorizeTokenResource>
 */
#[WithMonologChannel('user')]
final readonly class LogInUserProcessor implements ProcessorInterface
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private LoggerInterface $logger,
    ) {
    }

    #[\Override]
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): AuthorizeTokenResource
    {
        Assert::isInstanceOf($data, PostLoginUserResource::class);
        Assert::notNull($data->email);
        Assert::notNull($data->password);

        try {
            $userAuthenticated = $this->commandBus->dispatch(
                new LogInUserServiceCommand(
                    $data->email,
                    $data->password,
                ),
            );
        } catch (InvalidTokenException $exception) {
            $this->logger->error(
                'Log in user: Invalid token',
                [
                    'exception' => $exception,
                    'provider' => 'firebase',
                ],
            );

            throw new AuthenticationException($exception->getMessage());
        } catch (ExpiredTokenException $exception) {
            $this->logger->error(
                'Log in user: Token expired',
                [
                    'exception' => $exception,
                    'provider' => 'firebase',
                ],
            );

            throw new AuthenticationException($exception->getMessage());
        } catch (InvalidPayloadException $exception) {
            $this->logger->error(
                'Log in user: Invalid payload',
                [
                    'exception' => $exception,
                    'provider' => 'firebase',
                ],
            );

            throw new AuthenticationException($exception->getMessage());
        }

        return AuthorizeTokenResource::fromValue($userAuthenticated);
    }
}
