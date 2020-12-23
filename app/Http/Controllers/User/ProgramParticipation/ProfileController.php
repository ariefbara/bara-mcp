<?php

namespace App\Http\Controllers\User\ProgramParticipation;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\User\UserBaseController;
use Participant\Application\Service\User\RemoveParticipantProfile;
use Participant\Application\Service\User\SubmitParticipantProfile;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Participant\ParticipantProfile as ParticipantProfile2;
use Participant\Domain\Model\UserParticipant;
use Query\Application\Service\User\ViewParticipantProfile;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToUserFinder;

class ProfileController extends UserBaseController
{

    public function submit($programParticipationId, $programsProfileFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToUserFinder($fileInfoRepository, $this->userId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        
        $this->buildSubmitService()->execute(
                $this->userId(), $programParticipationId, $programsProfileFormId, $formRecordData);
        
        return $this->show($programParticipationId, $programsProfileFormId);
    }

    public function remove($programParticipationId, $programsProfileFormId)
    {
        $this->buildRemoveService()->execute($this->userId(), $programParticipationId, $programsProfileFormId);
        return $this->commandOkResponse();
    }

    public function showAll($programParticipationId)
    {
        $participantProfiles = $this->buildViewService()->showAll(
                $this->userId(), $programParticipationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($participantProfiles);
        foreach ($participantProfiles as $participantProfile) {
            $result["list"][] = [
                "id" => $participantProfile->getId(),
                "submitTime" => $participantProfile->getSubmitTimeString(),
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

    public function show($programParticipationId, $programsProfileFormId)
    {
        $participantProfile = $this->buildViewService()->showByProgramsProfileFormId(
                $this->userId(), $programParticipationId, $programsProfileFormId);

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

    protected function buildSubmitService()
    {
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);
        
        return new SubmitParticipantProfile($userParticipantRepository, $programsProfileFormRepository);
    }

    protected function buildRemoveService()
    {
        $participantProfileRepository = $this->em->getRepository(ParticipantProfile2::class);
        $userParticipantRepository = $this->em->getRepository(UserParticipant::class);
        return new RemoveParticipantProfile($participantProfileRepository, $userParticipantRepository);
    }

}
