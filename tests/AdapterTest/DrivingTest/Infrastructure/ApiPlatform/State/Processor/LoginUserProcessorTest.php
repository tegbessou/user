<?php

declare(strict_types=1);

namespace EmpireDesAmis\User\Tests\AdapterTest\DrivingTest\Infrastructure\ApiPlatform\State\Processor;

use EmpireDesAmis\User\Tests\Shared\ApiTestCase;
use EmpireDesAmis\User\Tests\Shared\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;

final class LoginUserProcessorTest extends ApiTestCase
{
    use RefreshDatabase;

    public function testLoginUser(): void
    {
        $this->post('/api/users/login', [
            'email' => 'hugues.gobet@gmail.com',
            'password' => 'root',
        ]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertJsonContains([
            'token' => 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImYwOGU2ZTNmNzg4ZDYwMTk0MDA1ZGJiYzE5NDc0YmY5Mjg5ZDM5ZWEiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL3NlY3VyZXRva2VuLmdvb2dsZS5jb20vcG9jL',
        ]);
    }

    #[DataProvider('provideInvalidData')]
    public function testLoginUserWithInvalidData(
        array $payload,
        int $statusCode,
        array $violations,
    ): void {
        $this->post('/api/users/login', $payload);

        $this->assertResponseStatusCodeSame($statusCode);
        $this->assertJsonContains($violations);
    }

    public static function provideInvalidData(): \Generator
    {
        yield 'No data' => [
            'payload' => [],
            'statusCode' => 422,
            'violations' => [
                '@type' => 'ConstraintViolationList',
                'title' => 'An error occurred',
                'description' => 'email: Cette valeur ne doit pas être vide.
password: Cette valeur ne doit pas être vide.',
            ],
        ];

        yield 'Email not an email' => [
            'payload' => [
                'email' => 'hugues.gobet',
                'password' => 'root',
            ],
            'statusCode' => 422,
            'violations' => [
                '@type' => 'ConstraintViolationList',
                'title' => 'An error occurred',
                'description' => 'email: Cette valeur n\'est pas une adresse email valide.',
            ],
        ];
    }
}
