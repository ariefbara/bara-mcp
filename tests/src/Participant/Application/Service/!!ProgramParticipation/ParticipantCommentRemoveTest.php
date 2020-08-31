<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ParticipantComment;
use Tests\TestBase;

class ParticipantCommentRemoveTest extends TestBase
{
    protected $service;
    protected $programParticipationCompositionId;
    protected $participantCommentRepository, $participantComment, $participantCommentId = 'participantCommentId';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->programParticipationCompositionId = $this->buildMockOfClass(ProgramParticipationCompositionId::class);
        $this->participantCommentRepository = $this->buildMockOfInterface(ParticipantCommentRepository::class);
        $this->participantComment = $this->buildMockOfClass(ParticipantComment::class);
        $this->participantCommentRepository->expects($this->any())
                ->method('ofId')
                ->with($this->programParticipationCompositionId, $this->participantCommentId)
                ->willReturn($this->participantComment);
        
        $this->service = new ParticipantCommentRemove($this->participantCommentRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->programParticipationCompositionId, $this->participantCommentId);
    }
    public function test_execute_removeParticipantComment()
    {
        $this->participantComment->expects($this->once())
                ->method('remove');
        $this->execute();
    }
    public function test_execute_updateRepository()
    {
        $this->participantCommentRepository->expects($this->once())
                ->method('update');
        $this->execute();
    }
}
