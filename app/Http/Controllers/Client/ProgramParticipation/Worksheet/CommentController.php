<?php

namespace App\Http\Controllers\Client\ProgramParticipation\Worksheet;

use App\Http\Controllers\Client\ClientBaseController;
use Client\ {
    Application\Service\Client\ProgramParticipation\ParticipantCommentSubmitNew,
    Application\Service\Client\ProgramParticipation\ParticipantCommentSubmitReply,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\Worksheet\WorksheetCompositionId,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ParticipantComment,
    Domain\Model\Client\ProgramParticipation\Worksheet,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment
};
use Query\ {
    Application\Service\Client\ProgramParticipation\Worksheet\CommentView,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment as Comment2
};

class CommentController extends ClientBaseController
{

    public function submitNew($programParticipationId, $worksheetId)
    {
        $service = $this->buildSubmitNewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $message = $this->stripTagsInputRequest('message');
        $commentId = $service->execute($programParticipationCompositionId, $worksheetId, $message);
        
        $viewService = $this->buildViewService();
        $worksheetCompositionId = new WorksheetCompositionId($this->clientId(), $programParticipationId, $worksheetId);
        $comment = $viewService->showById($worksheetCompositionId, $commentId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($comment));
    }

    public function submitReply($programParticipationId, $worksheetId, $commentId)
    {
        $service = $this->buildSubmitReplyService();
        $worksheetCompositionId = new WorksheetCompositionId($this->clientId(), $programParticipationId, $worksheetId);
        $message = $this->stripTagsInputRequest('message');
        $commentId = $service->execute($worksheetCompositionId, $commentId, $message);
        
        $viewService = $this->buildViewService();
        $comment = $viewService->showById($worksheetCompositionId, $commentId);
        return $this->commandCreatedResponse($this->arrayDataOfComment($comment));
    }

    public function show($programParticipationId, $worksheetId, $commentId)
    {
        $service = $this->buildViewService();
        $worksheetCompositionId = new WorksheetCompositionId($this->clientId(), $programParticipationId, $worksheetId);
        $comment = $service->showById($worksheetCompositionId, $commentId);
        
        return $this->singleQueryResponse($this->arrayDataOfComment($comment));
    }

    public function showAll($programParticipationId, $worksheetId)
    {
        $service = $this->buildViewService();
        $worksheetCompositionId = new WorksheetCompositionId($this->clientId(), $programParticipationId, $worksheetId);
        $comments = $service->showAll($worksheetCompositionId, $this->getPage(), $this->getPageSize());
        
        $result = [];
        $result["total"] = count($comments);
        foreach ($comments as $comment) {
            $result['list'][] = [
                "id" => $comment->getId(),
                "message" => $comment->getMessage(),
                "submitTime" => $comment->getSubmitTimeString(),
            ];
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfComment(Comment2 $comment): array
    {
        $parent = (empty($comment->getParent()))? null:
                [
                    "id" => $comment->getParent()->getId(),
                    "message" => $comment->getParent()->getMessage(),
                    "submitTime" => $comment->getParent()->getSubmitTimeString(),
                    "removed" => $comment->getParent()->isRemoved(),
                ];
        
        $data = [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            "parent" => $parent,
            "participant" => null,
            "consultant" => null,
        ];
        if (!empty($comment->getParticipantComment())) {
            $data['participant'] = [
                "id" => $comment->getParticipantComment()->getParticipant()->getId(),
                "client" => [
                    "id" => $comment->getParticipantComment()->getParticipant()->getClient()->getId(),
                    "name" => $comment->getParticipantComment()->getParticipant()->getClient()->getName(),
                ],
            ];
        } elseif (!empty ($comment->getConsultantComment())) {
            $data['consultant'] = [
                "id" => $comment->getConsultantComment()->getConsultant()->getId(),
                "personnel" => [
                    "id" => $comment->getConsultantComment()->getConsultant()->getPersonnel()->getId(),
                    "name" => $comment->getConsultantComment()->getConsultant()->getPersonnel()->getName(),
                ],
            ];
        }
        return $data;
    }
    
    protected function buildSubmitNewService()
    {
        $participantCommentRepository = $this->em->getRepository(ParticipantComment::class);
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        
        return new ParticipantCommentSubmitNew(
                $participantCommentRepository, $programParticipationRepository, $worksheetRepository);
    }
    protected function buildSubmitReplyService()
    {
        $participantCommentRepository = $this->em->getRepository(ParticipantComment::class);
        $programParticipationRepository = $this->em->getRepository(ProgramParticipation::class);
        $commentRepoository = $this->em->getRepository(Comment::class);
        return new ParticipantCommentSubmitReply($participantCommentRepository, $programParticipationRepository, $commentRepoository);
    }
    protected function buildViewService()
    {
        $commentRepository = $this->em->getRepository(Comment2::class);
        return new CommentView($commentRepository);
    }

}
