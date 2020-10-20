<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation\Worksheet;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Config\EventList;
use Notification\ {
    Application\Listener\Firm\Program\Participant\ConsultantCommentRepliedByParticipantListener,
    Application\Service\GenerateConsultantCommentRepliedByParticipantNotification,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment as Comment3
};
use Participant\ {
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\ReplyComment,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\SubmitNewComment,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\Worksheet,
    Domain\Model\Participant\Worksheet\Comment as Comment2
};
use Query\ {
    Application\Service\Firm\Team\ProgramParticipation\Worksheet\ViewComment,
    Domain\Model\Firm\Program\Consultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Resources\Application\Event\Dispatcher;

class CommentController extends AsTeamMemberBaseController
{

    public function submitNew($teamId, $teamProgramParticipationId, $worksheetId)
    {
        $service = $this->buildSubmitService();
        $message = $this->stripTagsInputRequest("message");
        $commentId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $worksheetId,
                $message);

        $viewService = $this->buildViewService();
        $comment = $viewService->showById($teamId, $commentId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($comment));
    }

    public function reply($teamId, $teamProgramParticipationId, $worksheetId, $commentId)
    {
        $service = $this->buildReplyService();
        $message = $this->stripTagsInputRequest("message");
        $replyId = $service->execute(
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $worksheetId,
                $commentId, $message);

        $viewService = $this->buildViewService();
        $reply = $viewService->showById($teamId, $replyId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($reply));
    }

    public function show($teamId, $teamProgramParticipationId, $worksheetId, $commentId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);

        $service = $this->buildViewService();
        $comment = $service->showById($teamId, $commentId);

        return $this->singleQueryResponse($this->arrayDataOfComment($comment));
    }

    public function showAll($teamId, $teamProgramParticipationId, $worksheetId)
    {
        $this->authorizeClientIsActiveTeamMember($teamId);
        
        $service = $this->buildViewService();
        $comments = $service->showAll($teamId, $worksheetId, $this->getPage(), $this->getPageSize());

        $result = [];
        $result['total'] = count($comments);
        foreach ($comments as $comment) {
            $result['list'][] = $this->arrayDataOfComment($comment);
        }
        return $this->listQueryResponse($result);
    }

    protected function arrayDataOfComment(Comment $comment): array
    {

        return [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            'removed' => $comment->isRemoved(),
            "parent" => $this->arrayDataOfParentComment($comment->getParent()),
            "consultantComment" => $this->arrayDataOfConsultantComment($comment->getConsultantComment()),
        ];
    }

    protected function arrayDataOfParentComment(?Comment $comment): ?array
    {
        return empty($comment) ? null : [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            "removed" => $comment->isRemoved(),
            "consultantComment" => $this->arrayDataOfConsultantComment($comment->getConsultantComment()),
        ];
    }

    protected function arrayDataOfConsultantComment(?ConsultantComment $consultantComment): ?array
    {
        return empty($consultantComment) ? null : [
            'id' => $consultantComment->getId(),
            'consultant' => [
                'id' => $consultantComment->getConsultant()->getId(),
                'personnel' => [
                    'id' => $consultantComment->getConsultant()->getPersonnel()->getId(),
                    'name' => $consultantComment->getConsultant()->getPersonnel()->getName(),
                ],
            ],
        ];
    }

    protected function buildViewService()
    {
        $commentRepository = $this->em->getRepository(Comment::class);
        return new ViewComment($commentRepository);
    }

    protected function buildSubmitService()
    {
        $commentRepository = $this->em->getRepository(Comment2::class);
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);

        return new SubmitNewComment($commentRepository, $worksheetRepository, $teamMembershipRepository);
    }

    protected function buildReplyService()
    {
        $commentRepository = $this->em->getRepository(Comment2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::COMMENT_FROM_CONSULTANT_REPLIED, $this->buildConsultantCommentRepliedByParticipantListener());
        return new ReplyComment($commentRepository, $teamMembershipRepository, $dispatcher);
    }

    protected function buildConsultantCommentRepliedByParticipantListener()
    {
        $commentRepository = $this->em->getRepository(Comment3::class);
        $service = new GenerateConsultantCommentRepliedByParticipantNotification($commentRepository);
        return new ConsultantCommentRepliedByParticipantListener($service, $this->buildSendImmediateMail());
    }

}
