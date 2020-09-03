<?php

namespace Participant\Application\Service\ClientParticipant\Worksheet;

use Participant\ {
    Application\Service\ClientParticipantRepository,
    Application\Service\Participant\Worksheet\CommentRepository,
    Domain\Model\ClientParticipant,
    Domain\Model\Participant\Worksheet\Comment
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ReplyCommentTest extends TestBase
{

    protected $service;
    protected $commentRepository, $comment, $nextId = 'nextId';
    protected $clientParticipantRepository, $clientParticipant;
    protected $dispatcher;
    protected $firmId = 'firmId', $clientId = 'clientId', $programParticipationId = 'programParticipationId',
            $worksheetId = 'worksheetId', $commentId = 'commentId';
    protected $message = 'new comment';

    protected function setUp(): void
    {
        parent::setUp();
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->commentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);
        $this->commentRepository->expects($this->any())
                ->method('aCommentInClientParticipantWorksheet')
                ->with($this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId,
                        $this->commentId)
                ->willReturn($this->comment);

        $this->clientParticipant = $this->buildMockOfClass(ClientParticipant::class);
        $this->clientParticipantRepository = $this->buildMockOfInterface(ClientParticipantRepository::class);
        $this->clientParticipantRepository->expects($this->once())
                ->method('ofId')
                ->with($this->firmId, $this->clientId, $this->programParticipationId)
                ->willReturn($this->clientParticipant);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ReplyComment($this->commentRepository, $this->clientParticipantRepository,
                $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->firmId, $this->clientId, $this->programParticipationId, $this->worksheetId,
                        $this->commentId, $this->message);
    }
    public function test_execute_addCommentToRepository()
    {
        $reply = $this->buildMockOfClass(Comment::class);
        $this->clientParticipant->expects($this->once())
                ->method('replyComment')
                ->with($this->nextId, $this->comment)
                ->willReturn($reply);
        $this->commentRepository->expects($this->once())
                ->method('add')
                ->with($reply);
        $this->execute();
    }
    public function test_execute_dispatchClientParticipantToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->clientParticipant);
        $this->execute();
    }
    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
