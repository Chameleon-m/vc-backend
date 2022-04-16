<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CustomApiTestCase extends ApiTestCase
{

    // https://symfonycasts.com/screencast/api-platform-security/rock-solid-test-setup#encoding-the-user-password
    protected function createUser(string $email, string $phone, string $password, array $roles = ['ROLE_USER']): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPhone($phone);
        $user->setRoles($roles);

//        $hashedPassword = self::getContainer()->get(UserPasswordHasherInterface::class)
//            ->hashPassword($user, $password);
        $hashedPassword = self::getContainer()
            ->get(PasswordHasherFactoryInterface::class)
            ->getPasswordHasher(User::class)
            ->hash($password);
        $user->setPassword($hashedPassword);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    protected function logIn(Client $client, string $identifier, string $password): ResponseInterface
    {
        return $client->request('POST', '/api/login', [
            'json' => [
                'identifier' => $identifier,
                'password' => $password
            ],
        ]);
    }

    protected function logInCorrect(Client $client, string $identifier, string $password): ResponseInterface
    {
        $response = $this->logIn($client, $identifier, $password);
        self::assertResponseStatusCodeSame(204);
        return $response;
    }

    protected function logInUnauthorized(Client $client, string $identifier, string $password): ResponseInterface
    {
        $response = $this->logIn($client, $identifier, $password);
        self::assertResponseStatusCodeSame(401);
        return $response;
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::getContainer()->get('doctrine')->getManager();
    }
}