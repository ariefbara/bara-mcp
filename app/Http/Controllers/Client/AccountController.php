<?php

namespace App\Http\Controllers\Client;

use Client\ {
    Application\Service\ChangePassword,
    Application\Service\UpdateProfile,
    Domain\Model\Client as Client2
};
use Query\ {
    Application\Service\Firm\ViewClient,
    Domain\Model\Firm\Client
};

class AccountController extends ClientBaseController
{
    public function updateProfile()
    {
        $clientRepository = $this->em->getRepository(Client2::class);
        $service = new UpdateProfile($clientRepository);
        
        $firstName = $this->stripTagsInputRequest('firstName');
        $lastName = $this->stripTagsInputRequest('lastName');
        
        $service->execute($this->firmId(), $this->clientId(), $firstName, $lastName);
        
        $viewService = $this->buildViewService();
        $client = $viewService->showById($this->firmId(), $this->clientId());
        return $this->singleQueryResponse($this->arrayDataOfClient($client));
    }
    public function changePassword()
    {
        $clientRepository = $this->em->getRepository(Client2::class);
        $service = new ChangePassword($clientRepository);
        
        $previousPassword = $this->stripTagsInputRequest('previousPassword');
        $newPassword = $this->stripTagsInputRequest('newPassword');
        
        $service->execute($this->firmId(), $this->clientId(), $previousPassword, $newPassword);
        
        return $this->commandOkResponse();
    }
    
    protected function arrayDataOfClient(Client $client)
    {
        return [
            "id" => $client->getId(),
            "firstName" => $client->getFirstName(),
            "lastName" => $client->getLastName(),
            "email" => $client->getEmail(),
        ];
    }
    
    protected function buildViewService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        return new ViewClient($clientRepository);
    }
}
