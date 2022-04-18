<?php

namespace Client\Application\Listener;

use Client\Application\Service\ExecuteTask;
use Client\Domain\Task\AddClientParticipant;
use Config\EventList;
use Firm\Domain\Event\ProgramParticipationReceived;
use Resources\Domain\Event\CommonEvent;
use Tests\TestBase;

class AcknowledgeParticipationReceivedTest extends TestBase
{

    protected $service;
    protected $task;
    protected $listener;
    //
    protected $programApplicationReceivedEvent, $clientId = 'client-id';
    protected $programParticipationAcceptedEvent, $participantId = 'participant-id';
    protected $event;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = $this->buildMockOfClass(ExecuteTask::class);
        $this->task = $this->buildMockOfClass(AddClientParticipant::class);
        $this->listener = new TestableAcknowledgeParticipationReceived($this->service, $this->task);
        //
        $this->programApplicationReceivedEvent = new CommonEvent(EventList::PROGRAM_APPLICATION_RECEIVED,
                $this->clientId);
        $this->programParticipationAcceptedEvent = new CommonEvent(EventList::PROGRAM_PARTICIPATION_ACCEPTED,
                $this->participantId);
        $this->event = $this->programApplicationReceivedEvent;
    }

    protected function handle()
    {
        $this->listener->handle($this->event);
    }
    public function test_handle_setProgramApplicationReceivedEvent()
    {
        $this->handle();
        $this->assertSame($this->programApplicationReceivedEvent, $this->listener->programApplicationReceivedEventListened);
    }
    public function test_handle_setProgramApplicationReceivedEventAndProgramParticipationAlreadyListener_executeService()
    {
        $this->listener->programParticipationAcceptedEventListened = $this->programParticipationAcceptedEvent;
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->clientId, $this->task, $this->participantId);
        $this->handle();
    }
    public function test_handle_setProgramParticipationReceivedEvent()
    {
        $this->event = $this->programParticipationAcceptedEvent;
        $this->handle();
        $this->assertSame($this->programParticipationAcceptedEvent, $this->listener->programParticipationAcceptedEventListened);
    }
    public function test_handle_setProgramParticipationReceivedEventAndProgramApplicationReceivedAlreadyListener_executeService()
    {
        $this->event = $this->programParticipationAcceptedEvent;
        $this->listener->programApplicationReceivedEventListened = $this->programApplicationReceivedEvent;
        $this->service->expects($this->once())
                ->method('execute')
                ->with($this->clientId, $this->task, $this->participantId);
        $this->handle();
    }

}

class TestableAcknowledgeParticipationReceived extends AcknowledgeParticipationReceived
{
    public $clientExecuteTaskService;
    public $addClientParticipantTask;
    public $programApplicationReceivedEventListened;
    public $programParticipationAcceptedEventListened;
}
