<?php

namespace App\Http\Controllers\Client;

use Client\ {
    Application\Service\Client\ProgramRegistrationCancel,
    Application\Service\Client\ProgramRegistrationSubmit,
    Application\Service\Client\ProgramRegistrationView,
    Domain\Model\Client,
    Domain\Model\Client\ProgramRegistration,
    Domain\Model\Firm\Program
};

class ProgramRegistrationController extends ClientBaseController
{

    public function apply()
    {
        $service = $this->buildApplyService();
        $firmId = $this->stripTagsInputRequest('firmId');
        $programId = $this->stripTagsInputRequest('programId');
        
        $programRegistration = $service->execute($this->clientId(), $firmId, $programId);
        return $this->commandCreatedResponse($this->arrayDataOfProgramRegistration($programRegistration));
    }

    public function cancel($programRegistrationId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->clientId(), $programRegistrationId);
        return $this->commandOkResponse();
    }

    public function show($programRegistrationId)
    {
        $service = $this->buildViewService();
        $programRegistration = $service->showById($this->clientId(), $programRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramRegistration($programRegistration));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $programRegistrations = $service->showAll($this->clientId(), $this->getPage(), $this->getPageSize());

        $result = [];
        $result['total'] = count($programRegistrations);
        foreach ($programRegistrations as $programRegistration) {
            $result['list'][] = [
                "id" => $programRegistration->getId(),
                "concluded" => $programRegistration->isConcluded(),
                "note" => $programRegistration->getNote(),
                "program" => [
                    "id" => $programRegistration->getProgram()->getId(),
                    "name" => $programRegistration->getProgram()->getName(),
                    "removed" => $programRegistration->getProgram()->isRemoved(),
                ],
            ];
        }

        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfProgramRegistration(ProgramRegistration $programRegistration): array
    {
        return [
            "id" => $programRegistration->getId(),
            "program" => [
                "id" => $programRegistration->getProgram()->getId(),
                "name" => $programRegistration->getProgram()->getName(),
                "firm" => [
                    "id" => $programRegistration->getProgram()->getFirm()->getId(),
                    "name" => $programRegistration->getProgram()->getFirm()->getName(),
                ],
            ],
            "appliedTime" => $programRegistration->getAppliedTimeString(),
            "concluded" => $programRegistration->isConcluded(),
            "note" => $programRegistration->getNote(),
        ];
    }

    protected function buildApplyService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        $clientRepository = $this->em->getRepository(Client::class);
        $programRepository = $this->em->getRepository(Program::class);
        return new ProgramRegistrationSubmit($programRegistrationRepository, $clientRepository, $programRepository);
    }

    protected function buildCancelService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        return new ProgramRegistrationCancel($programRegistrationRepository);
    }

    protected function buildViewService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        return new ProgramRegistrationView($programRegistrationRepository);
    }
}
