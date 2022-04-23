<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use RefreshDatabaseTrait;

    public function testCreateUser(): void
    {
        $client = self::createClient();
        $user = $this->createUser('test-n@test.com', '79632482766', 'test');
        self::assertIsInt($user->getId());

        #

        $this->logInCorrect($client, 'test-n@test.com', 'test');
    }

    public function testCreateUserClient(): void
    {
        $this->getEntityManager()->beginTransaction();

        $client = self::createClient();
        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'test2@test.com',
                'phone' => '79632482763',
                'roles' => ["ROLE_USER"],
                'password' => 'test'
            ]
        ]);
        self::assertResponseStatusCodeSame(201);

        $this->getEntityManager()->commit(); // todo ?
        #

        $this->logInCorrect($client, 'test2@test.com', 'test');
    }

    public function testUpdateUser()
    {
        $client = self::createClient();
        $response = $this->logInCorrect($client, 'test2@test.com', 'test');
        $userCurrentIri = $response->getHeaders()['location'][0];

        $client->request('PUT', $userCurrentIri, [
            'json' => [
                'phone' => '79666666666'
            ]
        ]);

        self::assertResponseStatusCodeSame(200);
        self::assertJsonContains(['phone' => '79666666666']);

        #

        $this->logInCorrect($client, 'test3@test.com', 'test');

        $client->request('PUT', $userCurrentIri, [
            'json' => [
                'phone' => '79632482763'
            ]
        ]);
        self::assertResponseStatusCodeSame(403);
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