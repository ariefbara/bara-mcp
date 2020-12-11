<?php

namespace Participant\Application\Service\User;

use Participant\Application\Service\RegistrantProfileRepository;
use Participant\Domain\Model\UserRegistrant;
use Tests\TestBase;

class RemoveRegistrantProfileTest extends TestBase
{

    protected $userRegistrantRepository, $userRegistrant;
    protected $registrantProfileRepository;
    protected $service;
    protected $firmId = "firmId", $userId = "userId", $programRegistrationId = "userRegistrantId",
            $programsProfileFormId = "programProfileFormId";

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRegistrant = $this->buildMockOfClass(UserRegistrant::class);
        $this->userRegistrantRepository = $this->buildMockOfInterface(UserRegistrantRepository::class);
        $this->userRegistrantRepository->expects($this->any())
                ->method("aUserRegistrant")
                ->with($this->userId, $this->programRegistrationId)
                ->willReturn($this->userRegistrant);

        $this->registrantProfileRepository = $this->buildMockOfInterface(RegistrantProfileRepository::class);

        $this->service = new RemoveRegistrantProfile($this->registrantProfileRepository, $this->userRegistrantRepository);
    }

    protected function execute()
    {
        $this->service->execute($this->userId, $this->programRegistrationId, $this->programsProfileFormId);
    }

    public function test_execute_removeUserRegistrantProfile()
    {
        $this->registrantProfileRepository->expects($this->once())
                ->method("aRegistrantProfileCorrespondWithProgramsProfileForm")
                ->with($this->programRegistrationId, $this->programsProfileFormId);

        $this->userRegistrant->expects($this->once())
                ->method("removeProfile");
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->registrantProfileRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
