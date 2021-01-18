<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Client\Application\Service\Client\RemoveCV;
use Client\Application\Service\Client\SubmitCV;
use Client\Domain\DependencyModel\Firm\ClientCVForm;
use Client\Domain\Model\Client;
use Client\Domain\Model\Client\ClientCV as ClientCV2;
use Query\Application\Service\Firm\Client\ViewClientCV;
use Query\Domain\Model\Firm\Client\ClientCV;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToClientFinder;

class ClientCVController extends ClientBaseController
{
    public function submit($clientCVFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToClientFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $this->buildSubmitService()->execute($this->firmId(), $this->clientId(), $clientCVFormId, $formRecordData);
        return $this->show($clientCVFormId);
    }
    
    public function remove($clientCVFormId)
    {
        $this->buildRemoveService()->execute($this->firmId(), $this->clientId(), $clientCVFormId);
        return $this->commandOkResponse();
    }
    
    public function show($clientCVFormId)
    {
        $clientCV = $this->buildViewService()->showById($this->firmId(), $this->clientId(), $clientCVFormId);
        return $this->singleQueryResponse($this->arrayDataOfClientCV($clientCV));
    }
    
    public function showAll()
    {
        $clientCVs = $this->buildViewService()
                ->showAll($this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize());
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
    protected function buildSubmitService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $clientCVFormRepository = $this->em->getRepository(ClientCVForm::class);
        return new SubmitCV($clientRepository, $clientCVFormRepository);
    }
    protected function buildRemoveService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $clientCVRepository = $this->em->getRepository(ClientCV2::class);
        return new RemoveCV($clientRepository, $clientCVRepository);
    }
    
}
