<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Client\ViewClient;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Client\ClientBio;
use Query\Domain\Model\Firm\ClientSearchRequest;

class ClientController extends ClientBaseController
{
    public function show($id)
    {
        $client = $this->buildViewService()->showById($this->firmId(), $this->clientId(), $id);
        return $this->arrayDataOfClient($client);
    }
    
    public function showAll()
    {
        $clientSearchRequest = new ClientSearchRequest($this->getPage(), $this->getPageSize());
        $filters = json_decode($this->request->query('filters'), true);
        foreach ($filters['integerFieldFilters'] as $integerFieldFilter) {
            $clientSearchRequest->addIntegerFieldSearch(
                    $this->stripTagsVariable($integerFieldFilter['id']), 
                    $this->integerOfVariable($integerFieldFilter['value'])
            );
        }
        foreach ($filters['stringFieldFilters'] as $stringFieldFilter) {
            $clientSearchRequest->addStringFieldSearch(
                    $this->stripTagsVariable($stringFieldFilter['id']),
                    $this->stripTagsVariable($stringFieldFilter['value'])
            );
        }
        foreach ($filters['textAreaFieldFilters'] as $textAreaFieldFilter) {
            $clientSearchRequest->addTextAreaFieldSearch(
                    $this->stripTagsVariable($textAreaFieldFilter['id']),
                    $this->stripTagsVariable($textAreaFieldFilter['value'])
            );
        }
        foreach ($filters['singleSelectFieldFilters'] as $SingleSelectFieldFilter) {
            $clientSearchRequest->addSingleSelectFieldSearch(
                    $this->stripTagsVariable($SingleSelectFieldFilter['id']), 
                     filter_var_array($SingleSelectFieldFilter['listOfOptionId'], FILTER_SANITIZE_STRING)
            );
        }
        foreach ($filters['multiSelectFieldFilters'] as $MultiSelectFieldFilter) {
            $clientSearchRequest->addMultiSelectFieldSearch(
                    $this->stripTagsVariable($MultiSelectFieldFilter['id']), 
                     filter_var_array($MultiSelectFieldFilter['listOfOptionId'], FILTER_SANITIZE_STRING)
            );
        }
        
        return $this->buildViewService()->search($this->firmId(), $this->clientId(), $clientSearchRequest);
    }
    
    protected function arrayDataOfClient(Client $client): array
    {
        $clientBios = [];
        foreach ($client->iterateActiveClientBios() as $clientBio) {
            $clientBios[] = $this->arrayDataOfClientBio($clientBio);
        }
        return [
            'id' => $client->getId(),
            'name' => $client->getFullName(),
            'email' => $client->getEmail(),
            'clientBios' => $clientBios,
        ];
    }
    protected function arrayDataOfClientBio(ClientBio $clientBio): array
    {
        $clientBioData = (new FormRecordToArrayDataConverter())->convert($clientBio);
        $clientBioData['bioForm'] = [
            'id' => $clientBio->getBioForm()->getId(),
            'name' => $clientBio->getBioForm()->getName(),
        ];
        return $clientBioData;
    }
    
    protected function buildViewService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        return new ViewClient($clientRepository);
    }
}
