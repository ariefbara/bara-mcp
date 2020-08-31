<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\Domain\Model\Participant\Worksheet\ParticipantComment;
use Tests\TestBase;

class ReplyParticipantCommentTest extends TestBase
{
    protected $service;
    protected $participantCommentRepository, $participantComment, $nextId = 'nextId';
    
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
        
        $this->participantCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->service = new ReplyParticipantComment($this->participantCommentRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->participantCommentId, $this->message);
    }
    public function test_execute_addReplyParticipantCommentToRepository()
    {
        $reply = $this->buildMockOfClass(ParticipantComment::class);
        $this->participantComment->expects($this->once())
                ->method('createReply')
                ->with($this->nextId, $this->message)
                ->willReturn($reply);
        
        $this->participantCommentRepository->expects($this->once())
                ->method('add')
                ->with($reply);
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
