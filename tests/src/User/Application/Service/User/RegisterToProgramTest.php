<?php

namespace User\Application\Service\User;

use SharedContext\Domain\Model\Firm\Program;
use Tests\TestBase;
use User\ {
    Application\Service\UserRepository,
    Domain\Model\User,
    Domain\Model\User\ProgramRegistration
};

class RegisterToProgramTest extends TestBase
{

    protected $service;
    protected $programRegistrationRepository, $nextId = 'nextId';
    protected $userRepository, $user;
    protected $programRepository, $program;
    protected $userId = 'userId', $firmId = 'firmId', $programId = 'programId';

    protected function setUp(): void
    {
        parent::setUp();
        $this->programRegistrationRepository = $this->buildMockOfInterface(ProgramRegistrationRepository::class);
        $this->programRegistrationRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->user = $this->buildMockOfClass(User::class);
        $this->userRepository = $this->buildMockOfInterface(UserRepository::class);
        $this->userRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId)
                ->willReturn($this->user);

        $this->program = $this->buildMockOfClass(Program::class);
        $this->programRepository = $this->buildMockOfClass(ProgramRepository::class);
        $this->programRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->programId)
                ->willReturn($this->program);

        $this->service = new RegisterToProgram(
                $this->programRegistrationRepository, $this->userRepository, $this->programRepository);
    }
    protected function execute()
    {
        return $this->service->execute($this->userId, $this->firmId, $this->programId);
    }
    public function test_execute_addProgramRegistrationToRepository()
    {
        $programRegistration = $this->buildMockOfClass(ProgramRegistration::class);
        $this->user->expects($this->once())
                ->method('registerToProgram')
                ->with($this->nextId, $this->program)
                ->willReturn($programRegistration);
        
        $this->programRegistrationRepository->expects($this->once())
                ->method('add')
                ->with($programRegistration);
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
