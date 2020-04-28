<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\ {
    Application\Service\Client\ProgramParticipation\Worksheet\CommentRepository,
    Application\Service\Client\ProgramParticipation\Worksheet\WorksheetCompositionId,
    Application\Service\Client\ProgramParticipationRepository,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment
};
use Tests\TestBase;

class ParticipantCommentSubmitReplyTest extends TestBase
{

    protected $service;
    protected $worksheetCompositionId;
    protected $participantCommentRepository;
    protected $programParticipationRepository;
    protected $commentRepository, $comment, $commentId = 'commentId';
    protected $nextId = 'nextId', $message = 'new comment message';

    protected function setUp(): void
    {
        parent::setUp();
        $this->worksheetCompositionId = new WorksheetCompositionId('clientId', 'participantId', 'worksheetId');
        $this->participantCommentRepository = $this->buildMockOfInterface(ParticipantCommentRepository::class);
        $this->participantCommentRepository->expects($this->any())
                ->method('nextIdentity')
                ->willReturn($this->nextId);

        $this->programParticipationRepository = $this->buildMockOfInterface(ProgramParticipationRepository::class);
        $this->commentRepository = $this->buildMockOfInterface(CommentRepository::class);
        $this->comment = $this->buildMockOfClass(Comment::class);
        $this->commentRepository->expects($this->any())
                ->method('ofId')
                ->with($this->worksheetCompositionId, $this->commentId)
                ->willReturn($this->comment);

        $this->service = new ParticipantCommentSubmitReply(
                $this->participantCommentRepository, $this->programParticipationRepository, $this->commentRepository);
    }

    public function test_execute_addParticipantCommentToRepository()
    {
        $this->comment->expects($this->once())
                ->method('createReply')
                ->with($this->nextId, $this->message);
        $this->participantCommentRepository->expects($this->once())
                ->method('add');
        $this->service->execute($this->worksheetCompositionId, $this->commentId, $this->message);
    }

}
