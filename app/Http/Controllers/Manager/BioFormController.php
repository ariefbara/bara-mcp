<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\FormDataBuilder;
use App\Http\Controllers\FormToArrayDataConverter;
use Firm\Application\Service\Manager\CreateBioForm;
use Firm\Application\Service\Manager\DisableBioForm;
use Firm\Application\Service\Manager\EnableBioForm;
use Firm\Application\Service\Manager\UpdateBioForm;
use Firm\Domain\Model\Firm\BioForm as BioForm2;
use Firm\Domain\Model\Firm\Manager;
use Query\Application\Service\Firm\ViewBioForm;
use Query\Domain\Model\Firm\BioForm;

class BioFormController extends ManagerBaseController
{
    public function create()
    {
        $formData = (new FormDataBuilder($this->request))->build();
        $bioFormId = $this->buildCreateService()->execute($this->firmId(), $this->managerId(), $formData);
        
        $bioForm = $this->buildViewService()->showById($this->firmId(), $bioFormId);
        return $this->commandCreatedResponse($this->arrayDataOfBioForm($bioForm));
    }
    
    public function update($bioFormId)
    {
        $formData = (new FormDataBuilder($this->request))->build();
        $this->buildUpdateService()->execute($this->firmId(), $this->managerId(), $bioFormId, $formData);
        return $this->show($bioFormId);
    }
    
    public function disable($bioFormId)
    {
        $this->buildDisableService()->execute($this->firmId(), $this->managerId(), $bioFormId);
        return $this->show($bioFormId);
    }
    
    public function enable($bioFormId)
    {
        $this->buildEnableService()->execute($this->firmId(), $this->managerId(), $bioFormId);
        return $this->show($bioFormId);
    }
    
    public function showAll()
    {
        $this->authorizedUserIsFirmManager();
        
        $disableStatus = $this->filterBooleanOfQueryRequest("disableStatus");
        $bioForms = $this->buildViewService()
                ->showAll($this->firmId(), $this->getPage(), $this->getPageSize(), $disableStatus);
        
        $result = [];
        $result["total"] = count($bioForms);
        foreach ($bioForms as $bioForm) {
            $result["list"][] = [
                "id" => $bioForm->getId(),
                "name" => $bioForm->getName(),
                "disabled" => $bioForm->isDisabled(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($bioFormId)
    {
        $this->authorizedUserIsFirmManager();
        $bioForm = $this->buildViewService()->showById($this->firmId(), $bioFormId);
        return $this->singleQueryResponse($this->arrayDataOfBioForm($bioForm));
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
    protected function buildCreateService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $bioFormRepository = $this->em->getRepository(BioForm2::class);
        return new CreateBioForm($managerRepository, $bioFormRepository);
    }
    protected function buildUpdateService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $bioFormRepository = $this->em->getRepository(BioForm2::class);
        return new UpdateBioForm($managerRepository, $bioFormRepository);
    }
    protected function buildDisableService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $bioFormRepository = $this->em->getRepository(BioForm2::class);
        return new DisableBioForm($managerRepository, $bioFormRepository);
    }
    protected function buildEnableService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $bioFormRepository = $this->em->getRepository(BioForm2::class);
        return new EnableBioForm($managerRepository, $bioFormRepository);
    }
}
