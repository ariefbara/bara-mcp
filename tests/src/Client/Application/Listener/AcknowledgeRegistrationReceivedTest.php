<?php

namespace Client\Application\Listener;

use Client\Application\Service\ExecuteTask;
use Client\Domain\Task\AddClientRegistrant;
use Config\EventList;
use Firm\Domain\Event\ProgramRegistrationReceived;
use Resources\Domain\Event\CommonEvent;
use SharedContext\Domain\ValueObject\RegistrationStatus;
use Tests\TestBase;

class AcknowledgeRegistrationReceivedTest extends TestBase
{

    protected $service;
    protected $task;
    protected $listener;
    protected $programApplicationReceived, $clientId = 'client-id';
    protected $programRegistrationReceivedEvent, $registrantId = 'registrant-id';
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(ExecuteTask::class);
        $this->task = $this->buildMockOfClass(AddClientRegistrant::class);
        $this->listener = new TestableAcknowledgeRegistrationReceived($this->service, $this->task);
        //
        $this->programApplicationReceived = new CommonEvent(EventList::PROGRAM_APPLICATION_RECEIVED, $this->clientId);
        $this->programRegistrationReceivedEvent = new ProgramRegistrationReceived(
                $this->registrantId, new RegistrationStatus(RegistrationStatus::REGISTERED));
        $this->event = $this->programApplicationReceived;
    }

    protected function handle()
    {
        $this->listener->handle($this->event);
    }

    public function test_handle_programApplicationReceivedEvent_setProgramApplicationReceived()
    {
        $this->handle();
        $this->assertSame($this->programApplicationReceived, $this->listener->programApplicationReceivedEventListened);
        $this->assertNull($this->listener->programRegistraitonReceivedEventListened);
    }
    public function test_handle_programRegistrationReceivedEvent_setProgramRegistrationReceived()
    {
        $this->event = $this->programRegistrationReceivedEvent;
        $this->handle();
        $this->assertSame($this->programRegistrationReceivedEvent, $this->listener->programRegistraitonReceivedEventListened);
    }
    public function test_handle_programApplicationReceivedEventAndAprogramRegistrationAlreadyListened_executeService()
    {
        $this->listener->programRegistraitonReceivedEventListened = $this->programRegistrationReceivedEvent;
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->clientId, $this->task, $this->registrantId);
        $this->handle();
    }

}

class TestableAcknowledgeRegistrationReceived extends AcknowledgeRegistrationReceived
{
    public $clientExecuteTaskService;
    public $addClientRegistrantTask;
    public $programApplicationReceivedEventListened;
    public $programRegistraitonReceivedEventListened;
}
