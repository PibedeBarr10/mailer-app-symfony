<?php


namespace App\Service;


use App\Repository\ClientRepository;

class UserVerification
{
    private ClientRepository $clientRepository;

    public function __construct(ClientRepository $clientRepository)
    {
        $this->clientRepository = $clientRepository;
    }

    public function checkUser(string $email, string $password)
    {
        $client = $this->clientRepository->findOneBy([
            'email' => $email
        ]);

        if (!$client || $client->getPassword() !== $password) {
            return false;
        }
        return true;
    }
}