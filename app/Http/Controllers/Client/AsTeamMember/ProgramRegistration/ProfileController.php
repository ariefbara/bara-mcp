<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramRegistration;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Participant\Application\Service\Client\AsTeamMember\RemoveRegistrantProfile;
use Participant\Application\Service\Client\AsTeamMember\SubmitRegistrantProfile;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Registrant\RegistrantProfile as RegistrantProfile2;
use Participant\Domain\Model\TeamProgramRegistration;
use Query\Application\Service\Firm\Team\ViewRegistrantProfile;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToTeamFinder;

class ProfileController extends AsTeamMemberBaseController
{

    public function submit($teamId, $programRegistrationId, $programsProfileFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToTeamFinder($fileInfoRepository, $this->firmId(), $teamId);
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();

        $this->buildSubmitService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $programRegistrationId, $programsProfileFormId,
                $formRecordData);

        return $this->show($teamId, $programRegistrationId, $programsProfileFormId);
    }

    public function remove($teamId, $programRegistrationId, $programsProfileFormId)
    {
        $this->buildRemoveService()->execute(
                $this->firmId(), $this->clientId(), $teamId, $programRegistrationId, $programsProfileFormId);

        return $this->commandOkResponse();
    }

    public function show($teamId, $programRegistrationId, $programsProfileFormId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $registrantProfile = $this->buildViewService()
                ->showByProgramsProfileFormId($this->firmId(), $teamId, $programRegistrationId, $programsProfileFormId);

        return $this->singleQueryResponse($this->arrayDataOfRegistrantProfile($registrantProfile));
    }

    public function showAll($teamId, $programRegistrationId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $registrantProfiles = $this->buildViewService()
                ->showAll($this->firmId(), $teamId, $programRegistrationId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result["total"] = count($registrantProfiles);
        foreach ($registrantProfiles as $registrantProfile) {
            $result["list"][] = [
                "id" => $registrantProfile->getId(),
                "submitTime" => $registrantProfile->getSubmitTimeString(),
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

    protected function buildSubmitService()
    {
        $teamRegistrantRepository = $this->em->getRepository(TeamProgramRegistration::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);

        return new SubmitRegistrantProfile($teamRegistrantRepository, $teamMemberRepository,
                $programsProfileFormRepository);
    }

    protected function buildRemoveService()
    {
        $registrantProfileRepository = $this->em->getRepository(RegistrantProfile2::class);
        $teamMemberRepository = $this->em->getRepository(TeamMembership::class);
        $teamRegistrantRepository = $this->em->getRepository(TeamProgramRegistration::class);

        return new RemoveRegistrantProfile($registrantProfileRepository, $teamMemberRepository,
                $teamRegistrantRepository);
    }

}
