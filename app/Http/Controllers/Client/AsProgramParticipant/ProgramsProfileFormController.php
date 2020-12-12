<?php

namespace App\Http\Controllers\Client\AsProgramParticipant;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\Application\Service\Firm\Program\ViewProgramsProfileForm;
use Query\Domain\Model\Firm\Program\ProgramsProfileForm;

class ProgramsProfileFormController extends AsProgramParticipantBaseController
{
    
    public function showAll($programId)
    {
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
        $programsProfileForms = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
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
        $this->authorizedClientIsActiveProgramParticipant($programId);
        
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
    
}
