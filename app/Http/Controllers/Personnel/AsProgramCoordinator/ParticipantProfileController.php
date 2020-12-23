<?php

namespace App\Http\Controllers\Personnel\AsProgramCoordinator;

use App\Http\Controllers\FormRecordToArrayDataConverter;
use Query\Application\Service\Firm\Program\ViewParticipantProfile;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;

class ParticipantProfileController extends AsProgramCoordinatorBaseController
{
    public function showAll(string $programId, string $participantId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $participantProfiles = $this->buildViewService()
                ->showAll($this->firmId(), $programId, $participantId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($participantProfiles);
        foreach ($participantProfiles as $participantProfile) {
            $result["list"][] = [
                "id" => $participantProfile->getId(),
                "programsProfileForm" => [
                    "id" => $participantProfile->getProgramsProfileForm()->getId(),
                    "profileForm" => [
                        "id" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getId(),
                        "name" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getName(),
                    ],
                ],
            ];
        }
        return $this->listQueryResponse($result);
    }

    public function show(string $programId, string $participantProfileId)
    {
        $this->authorizedUserIsProgramCoordinator($programId);
        $participantProfile = $this->buildViewService()
                ->showById($this->firmId(), $programId, $participantProfileId);
        return $this->singleQueryResponse($this->arrayDataOfParticipantProfile($participantProfile));
    }

    protected function arrayDataOfParticipantProfile(ParticipantProfile $participantProfile): array
    {
        $result = (new FormRecordToArrayDataConverter())->convert($participantProfile);
        $result["id"] = $participantProfile->getId();
        $result["programsProfileForm"] = [
            "id" => $participantProfile->getProgramsProfileForm()->getId(),
            "profileForm" => [
                "id" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getId(),
                "name" => $participantProfile->getProgramsProfileForm()->getProfileForm()->getName(),
            ],
        ];
        return $result;
    }

    protected function buildViewService()
    {
        $participantProfileRepository = $this->em->getRepository(ParticipantProfile::class);
        return new ViewParticipantProfile($participantProfileRepository);
    }
}
