<?php

namespace App\Http\Controllers\Client;

use Client\ {
    Application\Service\Client\CancelProgramRegistration,
    Application\Service\Client\RegisterToProgram,
    Domain\Model\Client,
    Domain\Model\Client\ProgramRegistration
};
use Query\ {
    Application\Service\Firm\Client\ViewProgramRegistration,
    Domain\Model\Firm\Client\ClientRegistrant
};
use Resources\Application\Event\Dispatcher;
use SharedContext\Domain\Model\Firm\Program;

class ProgramRegistrationController extends ClientBaseController
{

    public function register()
    {
        $service = $this->buildRegisterService();
        
        $firmId = $this->firmId();
        $clientId = $this->clientId();
        $programId = $this->stripTagsInputRequest('programId');
        
        $programRegistrationId = $service->execute($firmId, $clientId, $programId);
        
        $viewService = $this->buildViewService();
        $programRegistration = $viewService->showById($this->firmId(), $this->clientId(), $programRegistrationId);
        return $this->commandCreatedResponse($this->arrayDataOfProgramRegistration($programRegistration));
    }

    public function cancel($programRegistrationId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->firmId(), $this->clientId(), $programRegistrationId);
        return $this->commandOkResponse();
    }

    public function show($programRegistrationId)
    {
        $service = $this->buildViewService();
        $programRegistration = $service->showById($this->firmId(), $this->clientId(), $programRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramRegistration($programRegistration));
    }

    public function showAll()
    {
        $service = $this->buildViewService();
        $concludedStatus = $this->filterBooleanOfQueryRequest('concludedStatus');
        $programRegistrations = $service->showAll(
                $this->firmId(), $this->clientId(), $this->getPage(), $this->getPageSize(), $concludedStatus);

        $result = [];
        $result['total'] = count($programRegistrations);
        foreach ($programRegistrations as $programRegistration) {
            $result['list'][] = $this->arrayDataOfProgramRegistration($programRegistration);
        }

        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfProgramRegistration(ClientRegistrant $programRegistration): array
    {
        return [
            "id" => $programRegistration->getId(),
            "program" => [
                "id" => $programRegistration->getProgram()->getId(),
                "name" => $programRegistration->getProgram()->getName(),
                "hasProfileForm" => $programRegistration->getProgram()->hasProfileForm(),
            ],
            "registeredTime" => $programRegistration->getRegisteredTimeString(),
            "concluded" => $programRegistration->isConcluded(),
            "note" => $programRegistration->getNote(),
        ];
    }
    
    protected function buildRegisterService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        $clientRepository = $this->em->getRepository(Client::class);
        $programRepository = $this->em->getRepository(Program::class);
        $dispatcher = new Dispatcher();
        return new RegisterToProgram($programRegistrationRepository, $clientRepository, $programRepository, $dispatcher);
    }
    protected function buildCancelService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        return new CancelProgramRegistration($programRegistrationRepository);
    }
    protected function buildViewService()
    {
        $programRegistrationRepository = $this->em->getRepository(ClientRegistrant::class);
        return new ViewProgramRegistration($programRegistrationRepository);
    }
}
