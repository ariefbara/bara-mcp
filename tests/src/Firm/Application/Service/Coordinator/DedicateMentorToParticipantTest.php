<?php

namespace Firm\Application\Service\Coordinator;

use Tests\src\Firm\Application\Service\Coordinator\DedicatedMentorTestBase;

class DedicateMentorToParticipantTest extends DedicatedMentorTestBase
{
    protected $service;
    protected $participantRepository;
    protected $participantId = 'participantId';
    protected $consultantRepository;
    protected $consultantId = 'consultantId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantRepository = $this->buildMockOfInterface(ParticipantRepository::class);
        $this->consultantRepository = $this->buildMockOfInterface(ConsultantRepository::class);
        
        $this->service = new DedicateMentorToParticipant($this->coordinatorRepository, $this->participantRepository, $this->consultantRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->personnelId, $this->programId, $this->participantId, $this->consultantId);
    }
    public function test_execute_coordinatorDedidateMentorToParticipant()
    {
        $this->participantRepository->expects($this->once())->method('ofId')->with($this->participantId);
        $this->consultantRepository->expects($this->once())->method('aConsultantOfId')->with($this->consultantId);
        $this->coordinator->expects($this->once())
                ->method('dedicateMentorToParticipant');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->coordinatorRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
    public function test_execute_returnDedicatedMentorIdFromCoordinatorOperation()
    {
        $this->coordinator->expects($this->once())
                ->method('dedicateMentorToParticipant')
                ->willReturn($dedicatedMentorId = 'dedicatedMentorId');
        $this->assertEquals($dedicatedMentorId, $this->execute());
    }
}
