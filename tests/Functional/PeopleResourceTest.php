<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\People;
use App\Test\CustomApiTestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class PeopleResourceTest extends CustomApiTestCase
{
    use RefreshDatabaseTrait;

    private function createPeople(Client $client, string $userCurrentIri): ResponseInterface
    {
        $response = $client->request('POST', '/api/people', [
            'json' => [
                'firstName' => 'FirstName',
                'secondName' => 'SecondName',
                'middleName' => 'MiddleName',
                'birthdayDate' => '2022-04-15',
                'addressResidental' => 'addressResidental',
                'contacts' => ['79632482762', 'test-n@test.com'],
                'owner' => $userCurrentIri,
//                'phones' => [],
//                'photos' => [],
//                'lastViewAddresses' => [],
            ],
        ]);

        self::assertResponseStatusCodeSame(201);

        return $response;
    }

    private function createPeopleIri(Client $client, string $userCurrentIri): string
    {
        $response = $this->createPeople($client, $userCurrentIri);
        return $response->getHeaders()['location'][0];
    }

    public function testCreatePeopleNotLogin(): void
    {
        $client = self::createClient();

        $client->request('POST', '/api/people', [
            'json' => [],
        ]);
        self::assertResponseStatusCodeSame(401);
    }

    public function testCreatePeopleLogin(): void
    {
        $client = self::createClient();
        $response = $this->logInCorrect($client, 'test@test.com', 'test');
        $userCurrentIri = $response->getHeaders()['location'][0];

        $peopleCreatedIri = $this->createPeopleIri($client, $userCurrentIri);
    }

    public function testCreatePeopleEmptyData(): void
    {
        $client = self::createClient();
        $this->logInCorrect($client, 'test@test.com', 'test');

        $client->request('POST', '/api/people', [
            'json' => [],
        ]);
        self::assertResponseStatusCodeSame(422);
    }

    public function testUpdatePeople(): void
    {
        $client = self::createClient();
        $response = $this->logInCorrect($client, 'test@test.com', 'test');
        $userCurrentIri = $response->getHeaders()['location'][0];

        $peopleCreatedIri = $this->createPeopleIri($client, $userCurrentIri);

        // iri '/api/people/{id}'
        $client->request('PUT', $peopleCreatedIri, [
            'json' => ['phone' => '11111111111']
        ]);

        self::assertResponseStatusCodeSame(200);

        # Incorrect owner

        $response = $this->logInCorrect($client, 'test1@test.com', 'test');
        $userCurrentIri = $response->getHeaders()['location'][0];
        $client->request('PUT', $peopleCreatedIri, [
            'json' => ['phone' => '22222222222']
        ]);

        self::assertResponseStatusCodeSame(403, 'only owner can updated');

        ## change owner

        $client->request('PUT', $peopleCreatedIri, [
            'json' => ['phone' => '22222222222', 'owner' => $userCurrentIri]
        ]);

        self::assertResponseStatusCodeSame(403, 'only owner can updated');
    }
}