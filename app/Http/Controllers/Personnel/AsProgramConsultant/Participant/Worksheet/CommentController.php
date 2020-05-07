<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant\Participant\Worksheet;

use App\Http\Controllers\Personnel\AsProgramConsultant\AsProgramConsultantBaseController;
use Query\ {
    Application\Service\Firm\Program\Participant\Worksheet\CommentView,
    Application\Service\Firm\Program\Participant\Worksheet\WorksheetCompositionId,
    Domain\Model\Firm\Program\Consultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\ParticipantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};

class CommentController extends AsProgramConsultantBaseController
{
    public function show($programId, $participantId, $worksheetId, $commentId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $worksheetCompositionId = new WorksheetCompositionId($this->firmId(), $programId, $participantId, $worksheetId);
        $comment = $service->showById($worksheetCompositionId, $commentId);
        
        return $this->singleQueryResponse($this->arrayDataOfComment($comment));
    }
    public function showAll($programId, $participantId, $worksheetId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);
        
        $service = $this->buildViewService();
        $worksheetCompositionId = new WorksheetCompositionId($this->firmId(), $programId, $participantId, $worksheetId);
        $comments = $service->showAll($worksheetCompositionId, $this->getPage(), $this->getPageSize());
        
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
            "participant" => $this->arrayDataOfParticipant($comment->getParticipantComment()),
            "consultant" => $this->arrayDataOfConsultant($comment->getConsultantComment()),
            "parent" => $this->arrayDataOfparentComment($comment->getParent()),
        ];
    }
    protected function arrayDataOfparentComment(?Comment $parentComment): ?array
    {
        if (empty($parentComment)) {
            return null;
        }
        return [
            "id" => $parentComment->getId(),
            "message" => $parentComment->getMessage(),
            "submitTime" => $parentComment->getSubmitTimeString(),
            "participant" => $this->arrayDataOfParticipant($parentComment->getParticipantComment()),
            "consultant" => $this->arrayDataOfConsultant($parentComment->getConsultantComment()),
        ];
        
    }
    protected function arrayDataOfParticipant(?ParticipantComment $participantComment): ?array
    {
        if (empty($participantComment)) {
            return null;
        }
        return [
            "id" => $participantComment->getParticipant()->getId(),
            "client" => [
                "id" => $participantComment->getParticipant()->getClient()->getId(),
                "name" => $participantComment->getParticipant()->getClient()->getName(),
            ],
        ];
    }
    protected function arrayDataOfConsultant(?ConsultantComment $consultantComment): ?array
    {
        if (empty($consultantComment)) {
            return null;
        }
        return [
            "id" => $consultantComment->getConsultant()->getId(),
            "personnel" => [
                "id" => $consultantComment->getConsultant()->getPersonnel()->getId(),
                "name" => $consultantComment->getConsultant()->getPersonnel()->getName(),
            ],
        ];
    }
    
    protected function buildViewService()
    {
        $commentRepository = $this->em->getRepository(Comment::class);
        return new CommentView($commentRepository);
    }
}
