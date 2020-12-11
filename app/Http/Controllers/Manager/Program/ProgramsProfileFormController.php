<?php

namespace App\Http\Controllers\Manager\Program;

use App\Http\Controllers\FormToArrayDataConverter;
use App\Http\Controllers\Manager\ManagerBaseController;
use Firm\Application\Service\Manager\AssignProfileFormToProgram;
use Firm\Application\Service\Manager\DisableProgramsProfileForm;
use Firm\Domain\Model\Firm\Manager;
use Firm\Domain\Model\Firm\ProfileForm;
use Firm\Domain\Model\Firm\Program;
use Firm\Domain\Model\Firm\Program\ProgramsProfileForm as ProgramsProfileForm2;
use Query\Application\Service\Firm\Program\ViewProgramsProfileForm;
use Query\Domain\Model\Firm\Program\ProgramsProfileForm;

class ProgramsProfileFormController extends ManagerBaseController
{
    
    public function assign($programId)
    {
        $profileFormId = $this->stripTagsInputRequest("profileFormId");
        $programsProfileFormId = $this->buildAssignService()
                ->execute($this->firmId(), $this->managerId(), $programId, $profileFormId);
        
        return $this->show($programId, $programsProfileFormId);
    }
    
    public function disable($programId, $programsProfileFormId)
    {
        $this->buildDisableService()->execute($this->firmId(), $this->managerId(), $programsProfileFormId);
        return $this->show($programId, $programsProfileFormId);
    }
    
    public function showAll($programId)
    {
        $this->authorizedUserIsFirmManager();
        
        $programsProfileForms = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize(), $enableOnly = false);
        
        $result = [];
        $result["total"] = count($programsProfileForms);
        foreach ($programsProfileForms as $programsProfileForm) {
            $result["list"][] = [
                "id" => $programsProfileForm->getId(),
                "disabled" => $programsProfileForm->isDisabled(),
                "profileForm" => [
                    "id" => $programsProfileForm->getProfileForm()->getId(),
                    "name" => $programsProfileForm->getProfileForm()->getName(),
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    public function show($programId, $programsProfileFormId)
    {
        $this->authorizedUserIsFirmManager();
        
        $programsProfileForm = $this->buildViewService()->showById($this->firmId(), $programId, $programsProfileFormId);
        return $this->singleQueryResponse($this->arrayDataOfProgramsProfileFormId($programsProfileForm));
    }
    
    protected function arrayDataOfProgramsProfileFormId(ProgramsProfileForm $programsProfileForm): array
    {
        $profileFormData = (new FormToArrayDataConverter())
                ->convert($programsProfileForm->getProfileForm());
        $profileFormData["id"] = $programsProfileForm->getProfileForm()->getId();
        return [
            "id" => $programsProfileForm->getId(),
            "disabled" => $programsProfileForm->isDisabled(),
            "profileForm" => $profileFormData,
        ];
    }
    protected function buildViewService()
    {
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);
        return new ViewProgramsProfileForm($programsProfileFormRepository);
    }
    protected function buildAssignService()
    {
        $programRepository = $this->em->getRepository(Program::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        $profileFormRepository = $this->em->getRepository(ProfileForm::class);
        
        return new AssignProfileFormToProgram($programRepository, $managerRepository, $profileFormRepository);
    }
    protected function buildDisableService()
    {
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm2::class);
        $managerRepository = $this->em->getRepository(Manager::class);
        
        return new DisableProgramsProfileForm($programsProfileFormRepository, $managerRepository);
    }
    
}
