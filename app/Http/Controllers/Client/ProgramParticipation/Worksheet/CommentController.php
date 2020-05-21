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
    Domain\Model\Firm\Program\Consultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\ParticipantComment as ParticipantComment2,
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
            $result['list'][] = $this->arrayDataOfComment($comment);
        }
        return $this->listQueryResponse($result);
    }
    
    protected function arrayDataOfComment(Comment2 $comment): array
    {
        return  [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            'removed' => $comment->isRemoved(),
            "parent" => $this->arrayDataOfParentComment($comment->getParent()),
            "participant" => $this->arrayDataOfParticipant($comment->getParticipantComment()),
            "consultant" => $this->arrayDataOfConsultant($comment->getConsultantComment()),
        ];
    }
    protected function arrayDataOfParentComment(?Comment2 $comment): ?array
    {
        return empty($comment)? null: [
            "id" => $comment->getId(),
            "message" => $comment->getMessage(),
            "submitTime" => $comment->getSubmitTimeString(),
            "removed" => $comment->isRemoved(),
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantComment $consultantComment): ?array
    {
        return empty($consultantComment)? null: [
            'id' => $consultantComment->getConsultant()->getId(),
            'personnel' => [
                'id' => $consultantComment->getConsultant()->getPersonnel()->getId(),
                'name' => $consultantComment->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }
    protected function arrayDataOfParticipant(?ParticipantComment2 $participantCOmment): ?array
    {
        return empty($participantCOmment)? null: [
            'id' => $participantCOmment->getParticipant()->getId(),
            'client' => [
                'id' => $participantCOmment->getParticipant()->getClient()->getId(),
                'name' => $participantCOmment->getParticipant()->getClient()->getName(),
            ],
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
        $commentRepository = $this->em->getRepository(Comment2::class);
        return new CommentView($commentRepository);
    }

}
