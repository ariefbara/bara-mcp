<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\ {
    Application\Service\Participant\WorksheetRepository,
    Domain\Model\Participant\Worksheet,
    Domain\Model\Participant\Worksheet\ParticipantComment
};
use Tests\TestBase;

class SubmitNewCommentTest extends TestBase
{
    protected $service;
    protected $participantCommentRepository, $nextId = 'nextId';
    protected $worksheetRepository, $worksheet;
    
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $worksheetId = 'worksheetId';
    
    protected $message = 'new comment';
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->participantCommentRepository = $this->buildMockOfInterface(ParticipantCommentRepository::class);
        $this->participantCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        
        $this->worksheet = $this->buildMockOfClass(Worksheet::class);
        $this->worksheetRepository = $this->buildMockOfClass(WorksheetRepository::class);
        $this->worksheetRepository->expects($this->any())
                ->method('aWorksheetOfClientParticipant')
                ->with($this->firmId, $this->clientId, $this->programId, $this->worksheetId)
                ->willReturn($this->worksheet);
        
        $this->service = new SubmitNewComment($this->participantCommentRepository, $this->worksheetRepository);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->message);
    }
    public function test_execute_addParticipantCommentToRepository()
    {
        $this->worksheet->expects($this->once())
                ->method('createParticipantComment')
                ->with($this->nextId, $this->message)
                ->willReturn($participantComment = $this->buildMockOfClass(ParticipantComment::class));
        
        $this->participantCommentRepository->expects($this->once())
                ->method('add')
                ->with($participantComment);
        
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }
}
