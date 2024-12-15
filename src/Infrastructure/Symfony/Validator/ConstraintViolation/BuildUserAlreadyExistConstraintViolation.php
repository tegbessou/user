<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Infrastructure\Symfony\Validator\ConstraintViolation;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Contracts\Translation\TranslatorInterface;

final readonly class BuildUserAlreadyExistConstraintViolation
{
    public function __construct(
        private TranslatorInterface $translator,
    ) {
    }

    public function build(string $email): ConstraintViolationList
    {
        $violations = new ConstraintViolationList();
        $violations->add(new ConstraintViolation(
            $this->translator->trans(
                'user.email.already_exists',
                ['email' => $email],
                'validators'
            ),
            null,
            [],
            $email,
            'email',
            $email
        ));

        return $violations;
    }
}
