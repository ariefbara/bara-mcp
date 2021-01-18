<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\Application\Service\Firm\ViewClientCVForm;
use Query\Domain\Model\Firm\ClientCVForm;

class ClientCVFormController extends ClientBaseController
{
    public function show($clientCVFormId)
    {
        $this->authorizeRequestFromActiveClient();
        $clientCVForm = $this->buildViewService()->showById($this->firmId(), $clientCVFormId);
        return $this->singleQueryResponse($this->arrayDataOfClientCVForm($clientCVForm));
    }
    public function showAll()
    {
        $this->authorizeRequestFromActiveClient();
        $disableStatus = false;
        $clientCVForms = $this->buildViewService()->showAll($this->firmId(), $this->getPage(), $this->getPageSize(), $disableStatus);
        $result = [];
        $result["total"] = count($clientCVForms);
        foreach ($clientCVForms as $clientCVForm) {
            $result["list"][] = [
                "id" => $clientCVForm->getId(),
                "profileForm" => [
                    "id" => $clientCVForm->getProfileForm()->getId(),
                    "name" => $clientCVForm->getProfileForm()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfClientCVForm(ClientCVForm $clientCVForm): array
    {
        $profileFormData = (new FormToArrayDataConverter())->convert($clientCVForm->getProfileForm());
        $profileFormData["id"] = $clientCVForm->getProfileForm()->getId();
        return [
            "id" => $clientCVForm->getId(),
            "disabled" => $clientCVForm->isDisabled(),
            "profileForm" => $profileFormData,
        ];
    }
    protected function buildViewService()
    {
        $clientCVFormRepository = $this->em->getRepository(ClientCVForm::class);
        return new ViewClientCVForm($clientCVFormRepository);
    }
}
