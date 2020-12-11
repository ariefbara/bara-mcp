<?php

namespace App\Http\Controllers\Client\AsProgramRegistrant;

use App\Http\Controllers\FormToArrayDataConverter;
use Query\Application\Service\Firm\Program\ViewProgramsProfileForm;
use Query\Domain\Model\Firm\Program\ProgramsProfileForm;

class ProgramsProfileFormController extends AsProgramRegistrantBaseController
{
    public function showAll($programId)
    {
        $this->authorizeClientInUnconcludedProgramRegistrant($programId);
        $programsProflieForms = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($programsProflieForms);
        foreach ($programsProflieForms as $programsProfileForm) {
            $result["list"][] = [
                "id" => $programsProfileForm->getId(),
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
        $this->authorizeClientInUnconcludedProgramRegistrant($programId);
        $programsProfileForm = $this->buildViewService()->showById($this->firmId(), $programId, $programsProfileFormId);
        return $this->singleQueryResponse($this->arrayDataOfProgramsProfileForm($programsProfileForm));
    }
    
    protected function arrayDataOfProgramsProfileForm(ProgramsProfileForm $programsProfileForm): array
    {
        $profileFormData = (new FormToArrayDataConverter())->convert($programsProfileForm->getProfileForm());
        $profileFormData["id"] = $programsProfileForm->getProfileForm()->getId();
        
        return [
            "id" => $programsProfileForm->getId(),
            "profileForm" => $profileFormData,
        ];
    }
    protected function buildViewService()
    {
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);
        return new ViewProgramsProfileForm($programsProfileFormRepository);
    }
}
