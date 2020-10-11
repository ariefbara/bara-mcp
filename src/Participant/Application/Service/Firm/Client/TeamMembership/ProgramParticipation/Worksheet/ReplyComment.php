<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Participant\Application\Service\{
    Firm\Client\TeamMembershipRepository,
    Participant\Worksheet\CommentRepository
};
use Resources\Application\Event\Dispatcher;

class ReplyComment
{

    /**
     *
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(CommentRepository $commentRepository,
            TeamMembershipRepository $teamMembershipRepository, Dispatcher $dispatcher)
    {
        $this->commentRepository = $commentRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamMembershipId, string $teamProgramParticipationId,
            string $worksheetId, string $commentId, string $message): string
    {
        $comment = $this->commentRepository
                ->aCommentBelongsToTeamParticipant($teamProgramParticipationId, $worksheetId, $commentId);
        $id = $this->commentRepository->nextIdentity();

        $reply = $this->teamMembershipRepository
                ->ofId($firmId, $clientId, $teamMembershipId)
                ->replyComment($comment, $id, $message);
        $this->commentRepository->add($reply);
        
        $this->dispatcher->dispatch($reply);

        return $id;
    }

}
