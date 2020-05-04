<?php

namespace App\Http\Controllers\Client;

use Client\ {
    Application\Service\ClientChangePassword,
    Application\Service\ClientChangeProfile,
    Domain\Model\Client
};
use Query\ {
    Application\Service\ClientView,
    Domain\Model\Client as Client2
};

class AccountController extends ClientBaseController
{
    public function updateProfile()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $service = new ClientChangeProfile($clientRepository);
        
        $name = $this->stripTagsInputRequest('name');
        $service->execute($this->clientId(), $name);
        
        $viewService = $this->buildViewService();
        $client = $viewService->showById($this->clientId());
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
    
    protected function arrayDataOfClient(Client2 $client)
    {
        return [
            "id" => $client->getId(),
            "name" => $client->getName(),
            "email" => $client->getEmail(),
        ];
    }
    
    protected function buildViewService()
    {
        $clientRepository = $this->em->getRepository(Client2::class);
        return new ClientView($clientRepository);
    }
}
