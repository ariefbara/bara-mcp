<?php

namespace Participant\Application\Service\UserParticipant\Worksheet;

use Participant\ {
    Application\Service\Participant\Worksheet\CommentRepository,
    Application\Service\UserParticipantRepository,
    Domain\Model\Participant\Worksheet\Comment,
    Domain\Model\UserParticipant
};
use Resources\Application\Event\Dispatcher;
use Tests\TestBase;

class ReplyCommentTest extends TestBase
{

    protected $service;
    protected $commentRepository, $comment, $nextId = 'nextId';
    protected $userParticipantRepository, $userParticipant;
    protected $dispatcher;
    protected $userId = 'userId', $userParticipantId = 'userParticipantId',
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
                ->method('aCommentInUserParticipantWorksheet')
                ->with($this->userId, $this->userParticipantId, $this->worksheetId, $this->commentId)
                ->willReturn($this->comment);

        $this->userParticipant = $this->buildMockOfClass(UserParticipant::class);
        $this->userParticipantRepository = $this->buildMockOfInterface(UserParticipantRepository::class);
        $this->userParticipantRepository->expects($this->once())
                ->method('ofId')
                ->with($this->userId, $this->userParticipantId)
                ->willReturn($this->userParticipant);

        $this->dispatcher = $this->buildMockOfClass(Dispatcher::class);

        $this->service = new ReplyComment($this->commentRepository, $this->userParticipantRepository, $this->dispatcher);
    }

    protected function execute()
    {
        return $this->service->execute(
                        $this->userId, $this->userParticipantId, $this->worksheetId, $this->commentId, $this->message);
    }

    public function test_execute_addCommentToRepository()
    {
        $reply = $this->buildMockOfClass(Comment::class);
        $this->userParticipant->expects($this->once())
                ->method('replyComment')
                ->with($this->nextId, $this->comment)
                ->willReturn($reply);
        $this->commentRepository->expects($this->once())
                ->method('add')
                ->with($reply);
        $this->execute();
    }

    public function test_execute_dispatchUserParticipantToDispatcher()
    {
        $this->dispatcher->expects($this->once())
                ->method('dispatch')
                ->with($this->userParticipant);
        $this->execute();
    }

    public function test_execute_returnNextId()
    {
        $this->assertEquals($this->nextId, $this->execute());
    }

}
