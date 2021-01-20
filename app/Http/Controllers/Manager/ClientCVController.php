<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Firm\Client\ViewClientCV;
use Query\Domain\Model\Firm\Client\ClientCV;

class ClientCVController extends ManagerBaseController
{
    public function show($clientId, $clientCVFormId)
    {
        $this->authorizedUserIsFirmManager();
        $clientCV = $this->buildViewService()->showById($this->firmId(), $clientId, $clientCVFormId);
        return $this->singleQueryResponse($this->arrayDataOfClientCV($clientCV));
    }
    
    public function showAll($clientId)
    {
        $this->authorizedUserIsFirmManager();
        $clientCVs = $this->buildViewService()
                ->showAll($this->firmId(), $clientId, $this->getPage(), $this->getPageSize());
        $result = [];
        $result["total"] = count($clientCVs);
        foreach ($clientCVs as $clientCV) {
            $result["list"][] = [
                "id" => $clientCV->getId(),
                "submitTime" => $clientCV->getSubmitTimeString(),
                "clientCVForm" => [
                    "id" => $clientCV->getClientCVForm()->getId(),
                    "profileForm" => [
                        "id" => $clientCV->getClientCVForm()->getProfileForm()->getId(),
                        "name" => $clientCV->getClientCVForm()->getProfileForm()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfClientCV(ClientCV $clientCV): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($clientCV);
        $result["id"] = $clientCV->getId();
        $result["clientCVForm"] = [
            "id" => $clientCV->getClientCVForm()->getId(),
            "profileForm" => [
                "id" => $clientCV->getClientCVForm()->getProfileForm()->getId(),
                "name" => $clientCV->getClientCVForm()->getProfileForm()->getName(),
            ],
        ];
        return $result;
    }
    protected function buildViewService()
    {
        $clientCVRepository = $this->em->getRepository(ClientCV::class);
        return new ViewClientCV($clientCVRepository);
    }
}
