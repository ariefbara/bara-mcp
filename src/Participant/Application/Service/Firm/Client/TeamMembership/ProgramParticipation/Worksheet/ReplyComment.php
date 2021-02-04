<?php

namespace Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet;

use Participant\Application\Service\Firm\Client\TeamMembershipRepository;
use Participant\Application\Service\Participant\Worksheet\CommentRepository;
use Resources\Application\Event\Dispatcher;

class ReplyComment
{

    /**
     *
     * @var MemberCommentRepository
     */
    protected $memberCommentRepository;

    /**
     *
     * @var TeamMembershipRepository
     */
    protected $teamMembershipRepository;

    /**
     * 
     * @var CommentRepository
     */
    protected $commentRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    public function __construct(MemberCommentRepository $memberCommentRepository,
            TeamMembershipRepository $teamMembershipRepository, CommentRepository $commentRepository,
            Dispatcher $dispatcher)
    {
        $this->memberCommentRepository = $memberCommentRepository;
        $this->teamMembershipRepository = $teamMembershipRepository;
        $this->commentRepository = $commentRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $clientId, string $teamId, string $teamProgramParticipationId,
            string $commentId, string $message): string
    {
        $id = $this->memberCommentRepository->nextIdentity();
        $comment = $this->commentRepository->ofId($commentId);

        $reply = $this->teamMembershipRepository
                ->aTeamMembershipCorrespondWithTeam($firmId, $clientId, $teamId)
                ->replyComment($comment, $id, $message);
        $this->memberCommentRepository->add($reply);

        $this->dispatcher->dispatch($reply);

        return $id;
    }

}
