<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Firm\Client\ViewClientBio;
use Query\Domain\Model\Firm\Client\ClientBio;

class ClientBioController extends ManagerBaseController
{
    public function show($clientId, $bioFormId)
    {
        $this->authorizedUserIsFirmManager();
        $clientBio = $this->buildViewService()->showById($this->firmId(), $clientId, $bioFormId);
        return $this->singleQueryResponse($this->arrayDataOfClientBio($clientBio));
    }
    
    public function showAll($clientId)
    {
        $this->authorizedUserIsFirmManager();
        $clientBios = $this->buildViewService()
                ->showAll($this->firmId(), $clientId, $this->getPage(), $this->getPageSize());
        $result = [];
        $result["total"] = count($clientBios);
        foreach ($clientBios as $clientBio) {
            $result["list"][] = [
                "id" => $clientBio->getId(),
                "submitTime" => $clientBio->getSubmitTimeString(),
                "bioForm" => [
                    "id" => $clientBio->getBioForm()->getId(),
                    "name" => $clientBio->getBioForm()->getName(),
                    "disabled" => $clientBio->getBioForm()->isDisabled(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfClientBio(ClientBio $clientBio): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($clientBio);
        $result["id"] = $clientBio->getId();
        $result["bioForm"] = [
            "id" => $clientBio->getBioForm()->getId(),
            "name" => $clientBio->getBioForm()->getName(),
            "disabled" => $clientBio->getBioForm()->isDisabled(),
        ];
        return $result;
    }
    protected function buildViewService()
    {
        $clientCVRepository = $this->em->getRepository(ClientBio::class);
        return new ViewClientBio($clientCVRepository);
    }
}
