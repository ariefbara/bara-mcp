<?php

namespace App\Http\Controllers\Client\AsTeamMember\ProgramParticipation\Worksheet;

use App\Http\Controllers\Client\AsTeamMember\AsTeamMemberBaseController;
use Config\EventList;
use Notification\Application\Listener\Firm\Program\Participant\ConsultantCommentRepliedByParticipantListener;
use Notification\Application\Service\GenerateConsultantCommentRepliedByParticipantNotification;
use Notification\Domain\Model\Firm\Program\Participant\Worksheet\Comment as Comment3;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\ReplyComment;
use Participant\Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\SubmitNewComment;
use Participant\Domain\DependencyModel\Firm\Client\TeamMember\MemberComment as MemberComment2;
use Participant\Domain\DependencyModel\Firm\Client\TeamMembership;
use Participant\Domain\Model\Participant\Worksheet;
use Participant\Domain\Model\Participant\Worksheet\Comment as Comment2;
use Query\Application\Service\Firm\Team\ProgramParticipation\Worksheet\ViewComment;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantComment;
use Query\Domain\Model\Firm\Program\Participant\Worksheet\Comment;
use Query\Domain\Model\Firm\Team\Member\MemberComment;
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
                $this->firmId(), $this->clientId(), $teamId, $teamProgramParticipationId, $commentId, $message);

        $viewService = $this->buildViewService();
        $reply = $viewService->showById($teamId, $replyId);
        
        $this->sendAndCloseConnection($this->arrayDataOfComment($reply), 201);
        $this->sendImmediateMail();
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
            "member" => $this->arrayDataOfMember($comment->getMemberComment()),
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
    protected function arrayDataOfMember(?MemberComment $memberComment): ?array
    {
        return empty($memberComment)? null : [
            'id' => $memberComment->getMember()->getId(),
            'client' => [
                'id' => $memberComment->getMember()->getClient()->getId(),
                'name' => $memberComment->getMember()->getClient()->getFullName(),
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
        $memberCommentRepository = $this->em->getRepository(MemberComment2::class);
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        return new SubmitNewComment($memberCommentRepository, $worksheetRepository, $teamMembershipRepository);
    }

    protected function buildReplyService()
    {
        
        $memberCommentRepository = $this->em->getRepository(MemberComment2::class);
        $teamMembershipRepository = $this->em->getRepository(TeamMembership::class);
        $commentRepository = $this->em->getRepository(Comment2::class);
        $dispatcher = new Dispatcher();
        $dispatcher->addListener(
                EventList::COMMENT_FROM_CONSULTANT_REPLIED, $this->buildConsultantCommentRepliedByParticipantListener());
        return new ReplyComment($memberCommentRepository, $teamMembershipRepository, $commentRepository, $dispatcher);
    }

    protected function buildConsultantCommentRepliedByParticipantListener()
    {
        $commentRepository = $this->em->getRepository(Comment3::class);
        $service = new GenerateConsultantCommentRepliedByParticipantNotification($commentRepository);
        return new ConsultantCommentRepliedByParticipantListener($service);
    }

}
