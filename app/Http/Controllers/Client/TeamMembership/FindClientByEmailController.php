<?php

namespace App\Http\Controllers\Client\TeamMembership;

use Query\ {
    Application\Service\Firm\Client\TeamMembership\ViewClient,
    Domain\Model\Firm\Client,
    Domain\Service\Firm\ClientFinder
};

class FindClientByEmailController extends TeamMembershipBaseController
{
    public function show($teamMembershipId)
    {
        $service = $this->buildViewService($teamMembershipId);
        $clientEmail = $this->stripTagQueryRequest("clientEmail");
        $client = $service->showByEmail($this->firmId(), $this->clientId(), $teamMembershipId, $clientEmail);
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
        $clientFinder = new ClientFinder($clientRepository);
        return new ViewClient($this->teamMembershipRepository(), $clientFinder);
    }
}
