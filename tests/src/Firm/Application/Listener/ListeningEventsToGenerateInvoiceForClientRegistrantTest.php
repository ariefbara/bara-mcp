<?php

namespace Firm\Application\Listener;

use Config\EventList;
use Firm\Application\Service\ExecuteResponsiveTask;
use Firm\Domain\Event\ProgramRegistrationReceived;
use Firm\Domain\Task\Responsive\GenerateInvoiceForClientRegistrant;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\TestBase;

class ListeningEventsToGenerateInvoiceForClientRegistrantTest extends TestBase
{
    protected $service;
    protected $task;
    protected $listener;
    protected $clientRegistrantAddedEvent, $clientRegistrantId = 'client-registrant-id';
    protected $programRegisrationReceivedEvent, $registrantId = 'registrant-id', $registrationStatus;
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(ExecuteResponsiveTask::class);
        $this->task = $this->buildMockOfClass(GenerateInvoiceForClientRegistrant::class);
        $this->listener = new TestableListeningEventsToGenerateInvoiceForClientRegistrant($this->service, $this->task);
        
        $this->clientRegistrantAddedEvent = new CommonEvent(EventList::CLIENT_REGISTRANT_CREATED, $this->clientRegistrantId);
        $this->registrationStatus = $this->buildMockOfClass(RegistrationStatus::class);
        $this->programRegisrationReceivedEvent = new ProgramRegistrationReceived($this->registrantId, $this->registrationStatus);
        
        $this->event = $this->clientRegistrantAddedEvent;
    }
    
    protected function handle()
    {
        $this->registrationStatus->expects($this->any())
                ->method('sameValueAs')
                ->willReturn(true);
        $this->listener->handle($this->event);
    }
    public function test_handle_aClientRegistrantAddedEvent_setClientRegistrantAdded()
    {
        $this->handle();
        $this->assertSame($this->clientRegistrantAddedEvent, $this->listener->clientRegistrantAdded);
    }
    public function test_handle_aClientRegistrantAddedEvent_preventSettingProgramRegistrationReceived()
    {
        $this->handle();
        $this->assertSame($this->clientRegistrantAddedEvent, $this->listener->clientRegistrantAdded);
        $this->assertNull($this->listener->programRegistrationReceived);
    }
    public function test_handle_aProgramRegistrationReceivedEvent_setProgramRegistrationReceived()
    {
        $this->event = $this->programRegisrationReceivedEvent;
        $this->handle();
        $this->assertSame($this->programRegisrationReceivedEvent, $this->listener->programRegistrationReceived);
    }
    public function test_handle_bothEventListened_executeService()
    {
        $this->listener->programRegistrationReceived = $this->programRegisrationReceivedEvent;
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->task, $this->clientRegistrantId);
        $this->handle();
    }
    public function test_handle_notSettlementRequiredRegistrationStatus_doNothing()
    {
        $this->registrationStatus->expects($this->once())
                ->method('sameValueAs')
                ->with(New RegistrationStatus(RegistrationStatus::SETTLEMENT_REQUIRED))
                ->willReturn(false);
        $this->listener->programRegistrationReceived = $this->programRegisrationReceivedEvent;
        $this->service->expects($this->never())
                ->method('execute');
        $this->handle();
    }
}

class TestableListeningEventsToGenerateInvoiceForClientRegistrant extends ListeningEventsToGenerateInvoiceForClientRegistrant
{
    public $executeResponsiveTaskService;
    public $responsiveTask;
    public $clientRegistrantAdded;
    public $programRegistrationReceived;
}
