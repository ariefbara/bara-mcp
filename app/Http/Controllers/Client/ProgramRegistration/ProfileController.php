<?php

namespace App\Http\Controllers\Client\ProgramRegistration;

use App\Http\Controllers\Client\ClientBaseController;
use App\Http\Controllers\FormRecordDataBuilder;
use App\Http\Controllers\FormRecordToArrayDataConverter;
use Participant\Application\Service\Client\RemoveRegistrantProfile;
use Participant\Application\Service\Client\SubmitRegistrantProfile;
use Participant\Domain\DependencyModel\Firm\Program\ProgramsProfileForm;
use Participant\Domain\Model\ClientRegistrant;
use Participant\Domain\Model\Registrant\RegistrantProfile as RegistrantProfile2;
use Query\Application\Service\Firm\Client\ViewRegistrantProfile;
use Query\Domain\Model\Firm\Program\Registrant\RegistrantProfile;
use SharedContext\Domain\Model\SharedEntity\FileInfo;
use SharedContext\Domain\Service\FileInfoBelongsToClientFinder;

class ProfileController extends ClientBaseController
{

    public function submit($programRegistrationId, $programsProfileFormId)
    {
        $fileInfoRepository = $this->em->getRepository(FileInfo::class);
        $fileInfoFinder = new FileInfoBelongsToClientFinder($fileInfoRepository, $this->firmId(), $this->clientId());
        $formRecordData = (new FormRecordDataBuilder($this->request, $fileInfoFinder))->build();
        $this->buildSubmitService()->execute(
                $this->firmId(), $this->clientId(), $programRegistrationId, $programsProfileFormId, $formRecordData);

        return $this->show($programRegistrationId, $programsProfileFormId);
    }

    public function remove($programRegistrationId, $programsProfileFormId)
    {
        $this->buildRemoveService()
                ->execute($this->firmId(), $this->clientId(), $programRegistrationId, $programsProfileFormId);

        return $this->commandOkResponse();
    }

    public function showAll($programRegistrationId)
    {
        $service = $this->buildViewService();

        $registrantProfiles = $service->showAll(
                $this->firmId(), $this->clientId(), $programRegistrationId, $this->getPage(), $this->getPageSize());

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
                $this->firmId(), $this->clientId(), $programRegistrationId, $programsProfileFormId);

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
        $clientRegistrantRepository = $this->em->getRepository(ClientRegistrant::class);
        $programsProfileFormRepository = $this->em->getRepository(ProgramsProfileForm::class);

        return new SubmitRegistrantProfile($clientRegistrantRepository, $programsProfileFormRepository);
    }

    protected function buildRemoveService()
    {
        $clientRegistrantRepository = $this->em->getRepository(ClientRegistrant::class);
        $registrantProfileRepository = $this->em->getRepository(RegistrantProfile2::class);
        return new RemoveRegistrantProfile($clientRegistrantRepository, $registrantProfileRepository);
    }

}
