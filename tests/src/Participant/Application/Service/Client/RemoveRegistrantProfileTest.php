<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\RegistrantProfileRepository;
use Participant\Domain\Model\ClientRegistrant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class RemoveRegistrantProfileTest extends TestBase
{
    protected $clientRegistrantRepository, $clientRegistrant;
    protected $registrantProfileRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $programRegistrationId = "clientRegistrantId",
            $programsProfileFormId = "programProfileFormId";

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrantRepository->expects($this->any())
                ->method("aClientRegistrant")
                ->with($this->firmId, $this->clientId, $this->programRegistrationId)
                ->willReturn($this->clientRegistrant);
        
        $this->registrantProfileRepository = $this->buildMockOfInterface(RegistrantProfileRepository::class);

        $this->service = new RemoveRegistrantProfile(
                $this->clientRegistrantRepository, $this->registrantProfileRepository);
    }
    
    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->programRegistrationId, $this->programsProfileFormId);
    }
    public function test_execute_ClientRegistrantRemoveProfile()
    {
        $this->registrantProfileRepository->expects($this->once())
                ->method("aRegistrantProfileCorrespondWithProgramsProfileForm")
                ->with($this->programRegistrationId, $this->programsProfileFormId);
        $this->clientRegistrant->expects($this->once())
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
