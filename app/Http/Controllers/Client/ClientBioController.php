<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Client\Application\Service\Client\RemoveBio;
use Client\Application\Service\Client\SubmitBio;
use Client\Domain\DependencyModel\Firm\BioForm;
use Client\Domain\Model\Client;
use Client\Domain\Model\Client\ClientBio as ClientCV2;
use Query\Application\Service\Firm\Client\ViewClientBio;
use Query\Domain\Model\Firm\Client\ClientBio;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToClientFinder;

class ClientBioController extends ClientBaseController
{
    public function submit($bioFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToClientFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $this->buildSubmitService()->execute($this->firmId(), $this->clientId(), $bioFormId, $formRecordData);
        return $this->show($bioFormId);
    }
    
    public function remove($bioFormId)
    {
        $this->buildRemoveService()->execute($this->firmId(), $this->clientId(), $bioFormId);
        return $this->commandOkResponse();
    }
    
    public function show($bioFormId)
    {
        $clientCV = $this->buildViewService()->showById($this->firmId(), $this->clientId(), $bioFormId);
        return $this->singleQueryResponse($this->arrayDataOfClientBio($clientCV));
    }
    
    public function showAll()
    {
        $clientBios = $this->buildViewService()
                ->showAll($this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize());
        $result = [];
        $result["total"] = count($clientBios);
        foreach ($clientBios as $clientBio) {
            $result["list"][] = [
                "id" => $clientBio->getId(),
                "submitTime" => $clientBio->getSubmitTimeString(),
                "bioForm" => [
                    "id" => $clientBio->getBioForm()->getId(),
                    "name" => $clientBio->getBioForm()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfClientBio(ClientBio $clientBio): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($clientBio);
        $result["id"] = $clientBio->getId();
        $result['bioForm'] = (new \App\Http\Controllers\FormToArrayDataConverter())->convert($clientBio->getBioForm());
//        $result["bioForm"] = [
//            "id" => $clientBio->getBioForm()->getId(),
//            "name" => $clientBio->getBioForm()->getName(),
//        ];
        return $result;
    }
    protected function buildViewService()
    {
        $clientCVRepository = $this->em->getRepository(ClientBio::class);
        return new ViewClientBio($clientCVRepository);
    }
    protected function buildSubmitService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $bioFormRepository = $this->em->getRepository(BioForm::class);
        return new SubmitBio($clientRepository, $bioFormRepository);
    }
    protected function buildRemoveService()
    {
        $clientRepository = $this->em->getRepository(Client::class);
        $clientBioRepository = $this->em->getRepository(ClientCV2::class);
        return new RemoveBio($clientRepository, $clientBioRepository);
    }
    
}
