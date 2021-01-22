<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\Application\Service\Firm\ViewBioForm;
use Query\Domain\Model\Firm\BioForm;

class BioFormController extends ClientBaseController
{
    public function show($bioFormId)
    {
        $this->authorizeRequestFromActiveClient();
        $bioForm = $this->buildViewService()->showById($this->firmId(), $bioFormId);
        return $this->singleQueryResponse($this->arrayDataOfBioForm($bioForm));
    }
    public function showAll()
    {
        $this->authorizeRequestFromActiveClient();
        $disableStatus = false;
        $bioForms = $this->buildViewService()->showAll($this->firmId(), $this->getPage(), $this->getPageSize(), $disableStatus);
        $result = [];
        $result["total"] = count($bioForms);
        foreach ($bioForms as $bioForm) {
            $result["list"][] = [
                "id" => $bioForm->getId(),
                "name" => $bioForm->getName(),
                "description" => $bioForm->getDescription(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfBioForm(BioForm $bioForm): array
    {
        $result = (new FormToArrayDataConverter())->convert($bioForm);
        $result["id"] = $bioForm->getId();
        $result["disabled"] = $bioForm->isDisabled();
        return $result;
    }
    protected function buildViewService()
    {
        $clientCVFormRepository = $this->em->getRepository(BioForm::class);
        return new ViewBioForm($clientCVFormRepository);
    }
}
