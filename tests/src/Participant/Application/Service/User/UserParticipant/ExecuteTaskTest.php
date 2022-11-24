<?php

namespace Participant\Application\Service\User\UserParticipant;

use Participant\Application\Service\User\UserParticipantRepository;
use Participant\Domain\Model\UserParticipant;
use Participant\Domain\Task\Participant\ParticipantTask;
use Tests\TestBase;

class ExecuteTaskTest extends TestBase
{
    protected $userParticipantRepository, $userParticipant, $userId = 'userId', $userParticipantId = 'userParticipantId';
    protected $service;
    //
    protected $task, $payload = 'string represent task payload';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        
        $this->service = new ExecuteTask($this->userParticipantRepository);
        
        $this->task = $this->buildMockOfInterface(ParticipantTask::class);
    }
    
    protected function execute()
    {
        $this->userParticipantRepository->expects($this->any())
                ->method('aUserParticipant')
                ->with($this->userId, $this->userParticipantId)
                ->willReturn($this->userParticipant);
        
        $this->service->execute($this->userId, $this->userParticipantId, $this->task, $this->payload);
    }
    public function test_execute_userParticipantExecuteTask()
    {
        $this->userParticipant->expects($this->once())
                ->method('executeTask')
                ->with($this->task, $this->payload);
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    
}
