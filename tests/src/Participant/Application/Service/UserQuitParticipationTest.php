<?php

namespace Participant\Application\Service;

use Participant\Domain\Model\UserParticipant;
use Tests\TestBase;

class UserQuitParticipationTest extends TestBase
{
    protected $service;
    protected $userParticipantRepository, $userParticipant;
    
    protected $userId = 'userId', $programParticipationId = 'programParticipationId';
    
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->userId, $this->programParticipationId)
                ->willReturn($this->userParticipant);
        
        $this->service = new UserQuitParticipation($this->userParticipantRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->userId, $this->programParticipationId);
    }
    public function test_execute_quitUserParticipant()
    {
        $this->userParticipant->expects($this->once())
                ->method('quit');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->userParticipantRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
