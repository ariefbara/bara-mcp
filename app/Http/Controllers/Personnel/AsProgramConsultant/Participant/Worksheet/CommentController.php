<?php

namespace App\Http\Controllers\Personnel\AsProgramConsultant\Participant\Worksheet;

use App\Http\Controllers\Personnel\AsProgramConsultant\AsProgramConsultantBaseController;
use Query\ {
    Application\Service\Firm\Program\Participant\Worksheet\ViewComment,
    Domain\Model\Firm\Program\Consultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};

class CommentController extends AsProgramConsultantBaseController
{

    public function show($programId, $participantId, $worksheetId, $commentId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);

        $service = $this->buildViewService();
        $comment = $service->showById($this->firmId(), $programId, $participantId, $worksheetId, $commentId);

        return $this->singleQueryResponse($this->arrayDataOfComment($comment));
    }

    public function showAll($programId, $participantId, $worksheetId)
    {
        $this->authorizedPersonnelIsProgramConsultant($programId);

        $service = $this->buildViewService();
        $comments = $service->showAll(
                $this->firmId(), $programId, $participantId, $worksheetId, $this->getPage(), $this->getPageSize());

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
            "removed" => $comment->isRemoved(),
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
            "removed" => $parentComment->isRemoved(),
            "consultant" => $this->arrayDataOfConsultant($parentComment->getConsultantComment()),
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
        return new ViewComment($commentRepository);
    }

}
