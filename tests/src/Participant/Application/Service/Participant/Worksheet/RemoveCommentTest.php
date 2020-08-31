<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\Domain\Model\Participant\Worksheet\ParticipantComment;
use Tests\TestBase;

class RemoveCommentTest extends TestBase
{
    protected $service;
    protected $participantCommentRepository, $participantComment;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $worksheetId = 'worksheetId', $participantCommentId = 'participantCommentId';
    protected $message = 'new comment';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantComment = $this->buildMockOfClass(ParticipantComment::class);
        $this->participantCommentRepository = $this->buildMockOfInterface(ParticipantCommentRepository::class);
        $this->participantCommentRepository->expects($this->any())
                ->method('aParticipantCommentOfClientParticipant')
                ->with($this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->participantCommentId)
                ->willReturn($this->participantComment);
        
        $this->service = new RemoveComment($this->participantCommentRepository);
    }
    
    protected function execute()
    {
        $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->participantCommentId);
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
