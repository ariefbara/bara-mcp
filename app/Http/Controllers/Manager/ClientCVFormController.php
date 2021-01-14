<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\FormToArrayDataConverter;
use Firm\Application\Service\Manager\AssignClientCVForm;
use Firm\Application\Service\Manager\DisableClientCVForm;
use Firm\Domain\Model\Firm\ClientCVForm as ClientCVForm2;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\ProfileForm;
use Query\Application\Service\Firm\ViewClientCVForm;
use Query\Domain\Model\Firm\ClientCVForm;

class ClientCVFormController extends ManagerBaseController
{
    public function assign()
    {
        $profileFormId = $this->stripTagsInputRequest("profileFormId");
        $clientCVFormId = $this->buildAssignService()->execute($this->firmId(), $this->managerId(), $profileFormId);
        return $this->show($clientCVFormId);
    }
    
    public function disable($clientCVFormId)
    {
        $this->buildDisableService()->execute($this->firmId(), $this->managerId(), $clientCVFormId);
        return $this->show($clientCVFormId);
    }
    
    public function showAll()
    {
        $this->authorizedUserIsFirmManager();
        
        $disableStatus = $this->filterBooleanOfQueryRequest("disableStatus");
        $clientCVForms = $this->buildViewService()
                ->showAll($this->firmId(), $this->getPage(), $this->getPageSize(), $disableStatus);
        
        $result = [];
        $result["total"] = count($clientCVForms);
        foreach ($clientCVForms as $clientCVForm) {
            $result["list"][] = [
                "id" => $clientCVForm->getId(),
                "disabled" => $clientCVForm->isDisabled(),
                "profileForm" => [
                    "id" => $clientCVForm->getProfileForm()->getId(),
                    "name" => $clientCVForm->getProfileForm()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($clientCVFormId)
    {
        $this->authorizedUserIsFirmManager();
        $clientCVForm = $this->buildViewService()->showById($this->firmId(), $clientCVFormId);
        return $this->singleQueryResponse($this->arrayDataOfClientCVForm($clientCVForm));
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
    protected function buildAssignService()
    {
        $managerRepository = $this->em->getRepository(Manager::class);
        $profileFormRepository = $this->em->getRepository(ProfileForm::class);
        return new AssignClientCVForm($managerRepository, $profileFormRepository);
    }
    protected function buildDisableService()
    {
        $clientCVFormRepository = $this->em->getRepository(ClientCVForm2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        return new DisableClientCVForm($clientCVFormRepository, $managerRepository);
    }
}
