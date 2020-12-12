<?php

namespace App\Http\Controllers\User\AsProgramParticipant;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\Application\Service\Firm\Program\ViewProgramsProfileForm;
use Query\Domain\Model\Firm\Program\ProgramsProfileForm;

class ProgramsProfileFormController extends AsProgramParticipantBaseController
{
    
    public function showAll($firmId, $programId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $programsProfileForms = $this->buildViewService()
                ->showAll($firmId, $programId, $this->getPage(), $this->getPageSize());
        
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
    
    public function show($firmId, $programId, $programsProfileFormId)
    {
        $this->authorizedUserIsActiveProgramParticipant($firmId, $programId);
        
        $programsProfileForm = $this->buildViewService()->showById($firmId, $programId, $programsProfileFormId);
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
    
}
