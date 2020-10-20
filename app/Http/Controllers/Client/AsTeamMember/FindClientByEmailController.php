<?php

namespace App\Http\Controllers\Client\AsTeamMember;

use Query\ {
    Application\Service\Firm\ViewClient,
    Domain\Model\Firm\Client
};

class FindClientByEmailController extends AsTeamMemberBaseController
{
    public function show($teamId)
    {
        $this->authorizeClientIsActiveTeamMemberWithAdminPriviledge($teamId);
        
        $service = $this->buildViewService();
        $email = $this->stripTagQueryRequest("clientEmail");
        $client = $service->showByEmail($this->firmId(), $email);
        return $this->singleQueryResponse($this->arrayDataOfClient($client));
    }
    
    protected function arrayDataOfClient(Client $client): Array
    {
        return [
            "id" => $client->getId(),
            "name" => $client->getFullName(),
            "email" => $client->getEmail(),
            "isActive" => $client->isActivated(),
        ];
    }
    protected function buildViewService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        return new ViewClient($clientRepository);
    }
}
