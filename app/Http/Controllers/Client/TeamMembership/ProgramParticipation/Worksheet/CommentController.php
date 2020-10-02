<?php

namespace App\Http\Controllers\Client\TeamMembership\ProgramParticipation\Worksheet;

use App\Http\Controllers\Client\TeamMembership\TeamMembershipBaseController;
use Participant\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\ReplyComment,
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\SubmitNewComment,
    Domain\DependencyModel\Firm\Client\TeamMembership,
    Domain\Model\Participant\Worksheet,
    Domain\Model\Participant\Worksheet\Comment as Comment2
};
use Query\{
    Application\Service\Firm\Client\TeamMembership\ProgramParticipation\Worksheet\ViewComment,
    Domain\Model\Firm\Program\Consultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};

class CommentController extends TeamMembershipBaseController
{

    public function submitNew($teamMembershipId, $teamProgramParticipationId, $worksheetId)
    {
        $service = $this->buildSubmitService();
        $message = $this->stripTagsInputRequest("message");
        $commentId = $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId,
                $message);

        $viewService = $this->buildViewService();
        $comment = $viewService->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId,
                $commentId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($comment));
    }

    public function reply($teamMembershipId, $teamProgramParticipationId, $worksheetId, $commentId)
    {
        $service = $this->buildReplyService();
        $message = $this->stripTagsInputRequest("message");
        $replyId = $service->execute(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId,
                $commentId, $message);

        $viewService = $this->buildViewService();
        $reply = $viewService->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId,
                $replyId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($reply));
    }

    public function show($teamMembershipId, $teamProgramParticipationId, $worksheetId, $commentId)
    {
        $this->authorizeActiveTeamMember($teamMembershipId);

        $service = $this->buildViewService();
        $comment = $service->showById(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId,
                $commentId);

        return $this->singleQueryResponse($this->arrayDataOfComment($comment));
    }

    public function showAll($teamMembershipId, $teamProgramParticipationId, $worksheetId)
    {
        $this->authorizeActiveTeamMember($teamMembershipId);
        $service = $this->buildViewService();
        $comments = $service->showAll(
                $this->firmId(), $this->clientId(), $teamMembershipId, $teamProgramParticipationId, $worksheetId,
                $this->getPage(), $this->getPageSize());
        
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
        return new ReplyComment($commentRepository, $teamMembershipRepository);
    }

}
