<?php

namespace App\Http\Controllers\User;

use Query\ {
    Application\Service\User\ViewProgramRegistration,
    Domain\Model\User\UserRegistrant
};
use SharedContext\Domain\Model\Firm\Program;
use User\ {
    Application\Service\User\CancelProgramRegistration,
    Application\Service\User\RegisterToProgram,
    Domain\Model\User,
    Domain\Model\User\ProgramRegistration
};

class ProgramRegistrationController extends UserBaseController
{
    public function register()
    {
        $service = $this->buildRegisterService();
        
        $firmId = $this->stripTagsInputRequest('firmId');
        $programId = $this->stripTagsInputRequest('programId');
        
        $programRegistrationId = $service->execute($this->userId(), $firmId, $programId);
        
        $viewService = $this->buildViewService();
        $programRegistration = $viewService->showById($this->userId(), $programRegistrationId);
        return $this->commandCreatedResponse($this->arrayDataOfProgramRegistration($programRegistration));
    }
    
    public function cancel($programRegistrationId)
    {
        $service = $this->buildCancelService();
        $service->execute($this->userId(), $programRegistrationId);
        return $this->commandOkResponse();
    }
    
    public function show($programRegistrationId)
    {
        $service = $this->buildViewService();
        $programRegistration = $service->showById($this->userId(), $programRegistrationId);
        return $this->singleQueryResponse($this->arrayDataOfProgramRegistration($programRegistration));
    }
    
    public function showAll()
    {
        $service = $this->buildViewService();
        $programRegisrations = $service->showAll($this->userId(), $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result['total'] = count($programRegisrations);
        foreach ($programRegisrations as $programRegistration) {
            $result['list'][] = $this->arrayDataOfProgramRegistration($programRegistration);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfProgramRegistration(UserRegistrant $programRegistration): array
    {
        return [
            'id' => $programRegistration->getId(),
            'program' => [
                'id' => $programRegistration->getProgram()->getId(),
                'name' => $programRegistration->getProgram()->getName(),
                'firm' => [
                    'id' => $programRegistration->getProgram()->getFirm()->getId(),
                    'name' => $programRegistration->getProgram()->getFirm()->getName(),
                ],
            ],
            'registeredTime' => $programRegistration->getRegisteredTimeString(),
            'concluded' => $programRegistration->isConcluded(),
            'note' => $programRegistration->getNote(),
        ];
    }
    
    protected function buildRegisterService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        $userRepository = $this->em->getRepository(User::class);
        $programRepository = $this->em->getRepository(Program::class);
        
        return new RegisterToProgram($programRegistrationRepository, $userRepository, $programRepository);
    }
    protected function buildCancelService()
    {
        $programRegistrationRepository = $this->em->getRepository(ProgramRegistration::class);
        return new CancelProgramRegistration($programRegistrationRepository);
    }
    protected function buildViewService()
    {
        $programRegistrationRepository = $this->em->getRepository(UserRegistrant::class);
        return new ViewProgramRegistration($programRegistrationRepository);
    }
}
