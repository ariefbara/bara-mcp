<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Firm\Program\ViewRegistrantProfile;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;

class RegistrantProfileController extends AsProgramCoordinatorBaseController
{

    public function showAll(string $programId, string $registrantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $registrantProfiles = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $registrantId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($registrantProfiles);
        foreach ($registrantProfiles as $registrantProfile) {
            $result["list"][] = [
                "id" => $registrantProfile->getId(),
                "programsProfileForm" => [
                    "id" => $registrantProfile->getProgramsProfileForm()->getId(),
                    "profileForm" => [
                        "id" => $registrantProfile->getProgramsProfileForm()->getProfileForm()->getId(),
                        "name" => $registrantProfile->getProgramsProfileForm()->getProfileForm()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show(string $programId, string $registrantProfileId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $registrantProfile = $this->buildViewService()
                ->showById($this->firmId(), $programId, $registrantProfileId);
        return $this->singleQueryResponse($this->arrayDataOfRegistrantProfile($registrantProfile));
    }

    protected function arrayDataOfRegistrantProfile(RegistrantProfile $registrantProfile): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($registrantProfile);
        $result["id"] = $registrantProfile->getId();
        $result["programsProfileForm"] = [
            "id" => $registrantProfile->getProgramsProfileForm()->getId(),
            "profileForm" => [
                "id" => $registrantProfile->getProgramsProfileForm()->getProfileForm()->getId(),
                "name" => $registrantProfile->getProgramsProfileForm()->getProfileForm()->getName(),
            ],
        ];
        return $result;
    }

    protected function buildViewService()
    {
        $registrantProfileRepository = $this->em->getRepository(RegistrantProfile::class);
        return new ViewRegistrantProfile($registrantProfileRepository);
    }

}
