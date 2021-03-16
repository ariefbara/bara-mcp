<?php

namespace App\Http\Controllers\Manager;

use Query\Application\Service\Firm\ViewClient;
use Query\Domain\Model\Firm\Client;

class ClientController extends ManagerBaseController
{
    public function showAll()
    {
        $this->authorizedUserIsFirmManager();
        $activatedStatus = $this->filterBooleanOfQueryRequest("activatedStatus");
        $clients = $this->buildViewService()
                ->showAll($this->firmId(), $this->getPage(), $this->getPageSize(), $activatedStatus);
        $result = [];
        $result["total"] = count($clients);
        foreach ($clients as $client) {
            $result["list"][] = $this->arrayDataOfClient($client);
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($clientId)
    {
        $this->authorizedUserIsFirmManager();
        $client = $this->buildViewService()->showById($this->firmId(), $clientId);
        return $this->singleQueryResponse($this->arrayDataOfClient($client));
    }
    
    protected function arrayDataOfClient(Client $client): array
    {
        return [
            "id" => $client->getId(),
            "name" => $client->getFullName(),
            "email" => $client->getEmail(),
            "signupTime" => $client->getSignupTimeString(),
            "activated" => $client->isActivated(),
        ];
    }
    protected function buildViewService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        return new ViewClient($clientRepository);
    }
}
