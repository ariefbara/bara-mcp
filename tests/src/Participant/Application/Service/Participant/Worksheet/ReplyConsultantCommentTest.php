<?php

namespace Participant\Application\Service\Participant\Worksheet;

use Participant\ {
    Application\Service\ClientParticipantRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet\ConsultantComment,
    Domain\Model\Participant\Worksheet\ParticipantComment
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ReplyConsultantCommentTest extends TestBase
{

    protected $service;
    protected $participantCommentRepository, $nextId = 'nextId';
    protected $consultantCommentRepository, $consultantComment;
    protected $clientParticipantRepository, $clientParticipant;
    protected $dispatcher;
    protected $firmId = 'firmId', $clientId = 'clientId', $programId = 'programId', $worksheetId = 'worksheetId', $consultantCommentId = 'consultantCommentId';
    protected $message = 'new comment';

    protected function setUp(): void
    {
        parent::setUp();
        $this->participantCommentRepository = $this->buildMockOfInterface(ParticipantCommentRepository::class);
        $this->participantCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->consultantComment = $this->buildMockOfClass(ConsultantComment::class);
        $this->consultantCommentRepository = $this->buildMockOfInterface(ConsultantCommentRepository::class);
        $this->consultantCommentRepository->expects($this->any())
                ->method('aConsultantCommentOfClientParticipant')
                ->with($this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->consultantCommentId)
                ->willReturn($this->consultantComment);

        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->any())
                ->method('ofId')
                ->with($this->firmId, $this->clientId, $this->programId)
                ->willReturn($this->clientParticipant);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ReplyConsultantComment(
                $this->participantCommentRepository, $this->consultantCommentRepository,
                $this->clientParticipantRepository, $this->dispatcher);
    }
    
    protected function execute()
    {
        return $this->service->execute($this->firmId, $this->clientId, $this->programId, $this->worksheetId, $this->consultantCommentId, $this->message);
    }
    public function test_execute_addReplyToRepository()
    {
        $reply = $this->buildMockOfClass(ParticipantComment::class);
        
        $this->clientParticipant->expects($this->once())
                ->method('replyToConsultantComment')
                ->with($this->nextId, $this->consultantComment, $this->message)
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
    public function test_execute_dispatcheClientParticipantToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->clientParticipant);
        $this->execute();
    }

}
