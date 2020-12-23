<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Participant\Application\Service\Client\AsTeamMember\RemoveParticipantProfile;
use Participant\Application\Service\Client\AsTeamMember\SubmitParticipantProfile;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Participant\ParticipantProfile as ParticipantProfile2;
use Participant\Domain\Model\TeamProgramParticipation;
use Query\Application\Service\Firm\Team\ViewParticipantProfile;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToTeamFinder;

class ProfileController extends AsTeamMemberBaseController
{

    public function submit($teamId, $teamProgramParticipationId, $programsProfileFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToTeamFinder($fileInfoRepository, $this->firmId(), $teamId);
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();

        $this->buildSubmitService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $programsProfileFormId,
                $formRecordData);

        return $this->show($teamId, $teamProgramParticipationId, $programsProfileFormId);
    }

    public function remove($teamId, $teamProgramParticipationId, $programsProfileFormId)
    {
        $this->buildRemoveService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $programsProfileFormId);

        return $this->commandOkResponse();
    }

    public function show($teamId, $teamProgramParticipationId, $programsProfileFormId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $participantProfile = $this->buildViewService()
                ->showByProgramsProfileFormId($this->firmId(), $teamId, $teamProgramParticipationId, $programsProfileFormId);

        return $this->singleQueryResponse($this->arrayDataOfParticipantProfile($participantProfile));
    }

    public function showAll($teamId, $teamProgramParticipationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $participantProfiles = $this->buildViewService()
                ->showAll($this->firmId(), $teamId, $teamProgramParticipationId, $this->getPage(), $this->getPageSize());

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
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);

        return new SubmitParticipantProfile($teamParticipantRepository, $teamMemberRepository,
                $programsProfileFormRepository);
    }

    protected function buildRemoveService()
    {
        $participantProfileRepository = $this->em->getRepository(ParticipantProfile2::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamParticipantRepository = $this->em->getRepository(TeamProgramParticipation::class);

        return new RemoveParticipantProfile($participantProfileRepository, $teamMemberRepository,
                $teamParticipantRepository);
    }

}
