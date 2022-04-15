<?php

namespace App\Tests\Functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class PeopleResourceTest extends CustomApiTestCase
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function testCreatePeople(): void
    {
        $client = self::createClient();

//        $client->request('POST', '/api/people', [
//            'json' => [],
//        ]);
//        self::assertResponseStatusCodeSame(401);

        #

        $this->createUser('test@test.com', '79632482761', 'test');
        $this->logIn($client, 'test@test.com', 'test');

//        $client->request('POST', '/api/people', [
//            'json' => [],
//        ]);
//        self::assertResponseStatusCodeSame(422);
    }
}