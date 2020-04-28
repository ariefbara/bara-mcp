<?php

namespace App\Http\Controllers\Client;

use Client\ {
    Application\Service\ClientChangePassword,
    Application\Service\ClientChangeProfile,
    Domain\Model\Client
};

class AccountController extends ClientBaseController
{
    public function updateProfile()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ClientChangeProfile($clientRepository);
        
        $name = $this->stripTagsInputRequest('name');
        $client = $service->execute($this->clientId(), $name);
        
        return $this->singleQueryResponse($this->arrayDataOfClient($client));
    }
    public function changePassword()
    {
        
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ClientChangePassword($clientRepository);
        $previousPassword = $this->stripTagsInputRequest('previousPassword');
        $newPassword = $this->stripTagsInputRequest('newPassword');
        
        $service->execute($this->clientId(), $previousPassword, $newPassword);
        return $this->commandOkResponse();
    }
    
    protected function arrayDataOfClient(Client $client)
    {
        return [
            "id" => $client->getId(),
            "name" => $client->getName(),
            "email" => $client->getEmail(),
        ];
    }
}
