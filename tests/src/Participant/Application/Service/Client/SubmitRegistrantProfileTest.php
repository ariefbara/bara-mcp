<?php

namespace Participant\Application\Service\Client;

use Participant\Application\Service\ProgramsProfileFormRepository;
use Participant\Domain\Model\ClientRegistrant;
use SharedContext\Domain\Model\SharedEntity\FormRecordData;
use Tests\TestBase;

class SubmitRegistrantProfileTest extends TestBase
{

    protected $clientRegistrantRepository, $clientRegistrant;
    protected $programsProfileFormRepository;
    protected $service;
    protected $firmId = "firmId", $clientId = "clientId", $programRegistrationId = "clientRegistrantId",
            $programsProfileFormId = "programProfileFormId";
    protected $formRecordData;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clientRegistrant = $this->buildMockOfClass(ClientRegistrant::class);
        $this->clientRegistrantRepository = $this->buildMockOfInterface(ClientRegistrantRepository::class);
        $this->clientRegistrantRepository->expects($this->any())
                ->method("aClientRegistrant")
                ->with($this->firmId, $this->clientId, $this->programRegistrationId)
                ->willReturn($this->clientRegistrant);
        
        $this->programsProfileFormRepository = $this->buildMockOfInterface(ProgramsProfileFormRepository::class);

        $this->service = new SubmitRegistrantProfile(
                $this->clientRegistrantRepository, $this->programsProfileFormRepository);

        $this->formRecordData = $this->buildMockOfClass(FormRecordData::class);
    }

    protected function execute()
    {
        $this->service->execute(
                $this->firmId, $this->clientId, $this->programRegistrationId, $this->programsProfileFormId,
                $this->formRecordData);
    }
    public function test_execute_clientRegistrantSubmitProfile()
    {
        $this->programsProfileFormRepository->expects($this->once())->method("ofId");
        
        $this->clientRegistrant->expects($this->once())
                ->method("submitProfile")
                ->with($this->anything(), $this->formRecordData);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->clientRegistrantRepository->expects($this->once())
                ->method("update");
        $this->execute();
    }

}
