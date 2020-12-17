<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\ProgramsProfileFormRepository;
use Participant\Domain\Model\UserRegistrant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitRegistrantProfileTest extends TestBase
{
    protected $userRegistrantRepository, $userRegistrant;
    protected $programsProfileFormRepository;
    protected $service;
    protected $firmId = "firmId", $userId = "userId", $programRegistrationId = "userRegistrantId",
            $programsProfileFormId = "programProfileFormId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRegistrant = $this->buildMockOfClass(UserRegistrant::class);
        $this->userRegistrantRepository = $this->buildMockOfInterface(UserRegistrantRepository::class);
        $this->userRegistrantRepository->expects($this->any())
                ->method("aUserRegistrant")
                ->with($this->userId, $this->programRegistrationId)
                ->willReturn($this->userRegistrant);
        
        $this->programsProfileFormRepository = $this->buildMockOfInterface(ProgramsProfileFormRepository::class);

        $this->service = new SubmitRegistrantProfile(
                $this->userRegistrantRepository, $this->programsProfileFormRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->userId, $this->programRegistrationId, $this->programsProfileFormId, $this->formRecordData);
    }
    public function test_execute_submitUserRegistrantProfile()
    {
        $this->programsProfileFormRepository->expects($this->once())->method("ofId");
        
        $this->userRegistrant->expects($this->once())
                ->method("submitProfile")
                ->with($this->anything(), $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userRegistrantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }
}
