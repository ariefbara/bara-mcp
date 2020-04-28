<?php

namespace App\Http\Controllers\Client\ProgramParticipation\Worksheet;

use App\Http\Controllers\Client\ClientBaseController;
use Client\ {
    Application\Service\Client\ProgramParticipation\ParticipantCommentRemove,
    Application\Service\Client\ProgramParticipation\ParticipantCommentSubmitNew,
    Application\Service\Client\ProgramParticipation\ParticipantCommentSubmitReply,
    Application\Service\Client\ProgramParticipation\ProgramParticipationCompositionId,
    Application\Service\Client\ProgramParticipation\Worksheet\CommentView,
    Application\Service\Client\ProgramParticipation\Worksheet\WorksheetCompositionId,
    Domain\Model\Client\ProgramParticipation,
    Domain\Model\Client\ProgramParticipation\ParticipantComment,
    Domain\Model\Client\ProgramParticipation\Worksheet,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment
};

class CommentController extends ClientBaseController
{

    public function submitNew($programParticipationId, $worksheetId)
    {
        $service = $this->buildSubmitNewService();
        $programParticipationCompositionId = new ProgramParticipationCompositionId(
                $this->clientId(), $programParticipationId);
        $message = $this->stripTagsInputRequest('message');
        $participantComment = $service->execute($programParticipationCompositionId, $worksheetId, $message);
        return $this->commandCreatedResponse($this->arrayDataOfParticipantComment($participantComment));
    }

    public function submitReply($programParticipationId, $worksheetId, $commentId)
    {
        $service = $this->buildSubmitReplyService();
        $worksheetCompositionId = new WorksheetCompositionId($this->clientId(), $programParticipationId, $worksheetId);
        $message = $this->stripTagsInputRequest('message');
        $participantComment = $service->execute($worksheetCompositionId, $commentId, $message);
        
        return $this->commandCreatedResponse($this->arrayDataOfParticipantComment($participantComment));
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
    
    protected function arrayDataOfParticipantComment(ParticipantComment $participantComment): array
    {
        $parent = (empty($participantComment->getParent()))? null:
                [
                    "id" => $participantComment->getParent()->getId(),
                    "message" => $participantComment->getParent()->getMessage(),
                    "submitTime" => $participantComment->getParent()->getSubmitTimeString(),
                    "removed" => $participantComment->getParent()->isRemoved(),
                ];
        
        return [
            "id" => $participantComment->getId(),
            "message" => $participantComment->getMessage(),
            "submitTime" => $participantComment->getSubmitTimeString(),
            "parent" => $parent,
        ];
    }
    
    protected function arrayDataOfComment(Comment $comment): array
    {
        $parent = (empty($comment->getParent()))? null:
                [
                    "id" => $comment->getParent()->getId(),
                    "message" => $comment->getParent()->getMessage(),
                    "submitTime" => $comment->getParent()->getSubmitTimeString(),
                    "removed" => $comment->getParent()->isRemoved(),
                ];
        
        return [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            "parent" => $parent,
        ];
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
        $commentRepository = $this->em->getRepository(Comment::class);
        return new CommentView($commentRepository);
    }

}
