<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use RefreshDatabaseTrait;

    public function testCreateUser(): void
    {
        $user = $this->createUser('test-n@test.com', '79632482766', 'test');
        self::assertIsInt($user->getId());
    }

    # TODO: api login test

    public function testLoginUserEmail(): void
    {
        $client = self::createClient();
        $this->logInCorrect($client, 'test@test.com', 'test');
    }

    public function testLoginUserIncorrectEmail(): void
    {
        $client = self::createClient();
        $this->logInUnauthorized($client, 'test-incorrect@test.com', 'test');
    }

    public function testLoginUserPhone(): void
    {
        $client = self::createClient();
        $this->logInCorrect($client, '79632482761', 'test');
    }

    public function testLoginUserIncorrectPhone(): void
    {
        $client = self::createClient();
        $this->logInUnauthorized($client, '79632482769', 'test');
    }

    public function testLoginUserIncorrectPassword(): void
    {
        $client = self::createClient();
        $this->logInUnauthorized($client, '79632482769', 'test-incorrect');
    }
}