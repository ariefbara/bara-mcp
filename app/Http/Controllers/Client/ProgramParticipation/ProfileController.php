<?php

namespace App\Http\Controllers\Client\ProgramParticipation;

use App\Http\Controllers\Client\ClientBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Participant\Application\Service\Client\RemoveParticipantProfile;
use Participant\Application\Service\Client\SubmitParticipantProfile;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\ClientParticipant;
use Participant\Domain\Model\Participant\ParticipantProfile as ParticipantProfile2;
use Query\Application\Service\Firm\Client\ViewParticipantProfile;
use Query\Domain\Model\Firm\Program\Participant\ParticipantProfile;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToClientFinder;

class ProfileController extends ClientBaseController
{

    public function submit($programParticipationId, $programsProfileFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToClientFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $this->buildSubmitService()->execute(
                $this->firmId(), $this->clientId(), $programParticipationId, $programsProfileFormId, $formRecordData);

        return $this->show($programParticipationId, $programsProfileFormId);
    }

    public function remove($programParticipationId, $programsProfileFormId)
    {
        $this->buildRemoveService()
                ->execute($this->firmId(), $this->clientId(), $programParticipationId, $programsProfileFormId);

        return $this->commandOkResponse();
    }

    public function showAll($programParticipationId)
    {
        $service = $this->buildViewService();

        $participantProfiles = $service->showAll(
                $this->firmId(), $this->clientId(), $programParticipationId, $this->getPage(), $this->getPageSize());

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
                $this->firmId(), $this->clientId(), $programParticipationId, $programsProfileFormId);

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
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);

        return new SubmitParticipantProfile($clientParticipantRepository, $programsProfileFormRepository);
    }

    protected function buildRemoveService()
    {
        $clientParticipantRepository = $this->em->getRepository(ClientParticipant::class);
        $participantProfileRepository = $this->em->getRepository(ParticipantProfile2::class);
        return new RemoveParticipantProfile($clientParticipantRepository, $participantProfileRepository);
    }

}
