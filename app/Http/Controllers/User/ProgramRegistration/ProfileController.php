<?php

namespace App\Http\Controllers\User\ProgramRegistration;

use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use App\Http\Controllers\User\UserBaseController;
use Participant\Application\Service\User\RemoveRegistrantProfile;
use Participant\Application\Service\User\SubmitRegistrantProfile;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\Registrant\RegistrantProfile as RegistrantProfile2;
use Participant\Domain\Model\UserRegistrant;
use Query\Application\Service\User\ViewRegistrantProfile;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToUserFinder;

class ProfileController extends UserBaseController
{

    public function submit($programRegistrationId, $programsProfileFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToUserFinder($fileInfoRepository, $this->userId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        
        $this->buildSubmitService()->execute(
                $this->userId(), $programRegistrationId, $programsProfileFormId, $formRecordData);
        
        return $this->show($programRegistrationId, $programsProfileFormId);
    }

    public function remove($programRegistrationId, $programsProfileFormId)
    {
        $this->buildRemoveService()->execute($this->userId(), $programRegistrationId, $programsProfileFormId);
        return $this->commandOkResponse();
    }

    public function showAll($programRegistrationId)
    {
        $registrantProfiles = $this->buildViewService()->showAll(
                $this->userId(), $programRegistrationId, $this->getPage(), $this->getPageSize());

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

    public function show($programRegistrationId, $programsProfileFormId)
    {
        $registrantProfile = $this->buildViewService()->showByProgramsProfileFormId(
                $this->userId(), $programRegistrationId, $programsProfileFormId);

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

    protected function buildSubmitService()
    {
        $userRegistrantRepository = $this->em->getRepository(UserRegistrant::class);
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);
        
        return new SubmitRegistrantProfile($userRegistrantRepository, $programsProfileFormRepository);
    }

    protected function buildRemoveService()
    {
        $registrantProfileRepository = $this->em->getRepository(RegistrantProfile2::class);
        $userRegistrantRepository = $this->em->getRepository(UserRegistrant::class);
        return new RemoveRegistrantProfile($registrantProfileRepository, $userRegistrantRepository);
    }

}
