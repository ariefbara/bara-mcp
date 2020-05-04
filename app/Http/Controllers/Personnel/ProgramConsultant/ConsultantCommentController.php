<?php

namespace App\Http\Controllers\Personnel\ProgramConsultant;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Client\ {
    Application\Listener\ConsultantCommentOnWorksheetListener,
    Domain\Model\Client\ClientNotification,
    Domain\Model\Client\ProgramParticipation\Worksheet\Comment as CommentForClient
};
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentRemove,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentSubmitNew,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentSubmitReply,
    Application\Service\Firm\Personnel\ProgramConsultant\ProgramConsultantCompositionId,
    Domain\Event\ConsultantCommentOnWorksheetEvent,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Query\ {
    Application\Service\Firm\Personnel\PersonnelCompositionId,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentView,
    Domain\Model\Firm\Program\Consultant\ConsultantComment as ConsultantComment2,
    Domain\Model\Firm\Program\Participant\Worksheet
};
use Resources\Application\Event\Dispatcher;

class ConsultantCommentController extends PersonnelBaseController
{

    public function submitNew($programConsultantId)
    {
        $service = $this->buildSubmitNewService();
        $personnelCompositionId = new PersonnelCompositionId($this->firmId(), $this->personnelId());
        $participantId = $this->stripTagsInputRequest('participantId');
        $worksheetId = $this->stripTagsInputRequest('worksheetId');
        $message = $this->stripTagsInputRequest('message');

        $consultantCommentId = $service->execute(
                $personnelCompositionId, $programConsultantId, $participantId, $worksheetId, $message);

        $viewService = $this->buildViewService();
        $programConsultantCompositionId = new ProgramConsultantCompositionId(
                $this->firmId(), $this->personnelId(), $programConsultantId);
        $consultantComment = $viewService->showById($programConsultantCompositionId, $consultantCommentId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultantComment($consultantComment));
    }

    public function submitReply($programConsultantId)
    {
        $service = $this->buildSubmitReplyService();
        $personnelCompositionId = new PersonnelCompositionId($this->firmId(), $this->personnelId());
        $participantId = $this->stripTagsInputRequest('participantId');
        $worksheetId = $this->stripTagsInputRequest('worksheetId');
        $commentId = $this->stripTagsInputRequest('commentId');
        $message = $this->stripTagsInputRequest('message');

        $consultantCommentId = $service->execute(
                $personnelCompositionId, $programConsultantId, $participantId, $worksheetId, $commentId, $message);
        
        $viewService = $this->buildViewService();
        $programConsultantCompositionId = new ProgramConsultantCompositionId(
                $this->firmId(), $this->personnelId(), $programConsultantId);
        $consultantComment = $viewService->showById($programConsultantCompositionId, $consultantCommentId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultantComment($consultantComment));
    }

    public function remove($programConsultantId, $consultantCommentId)
    {
        $service = $this->buildRemoveService();
        $programConsultantCompositionId = new ProgramConsultantCompositionId(
                $this->firmId(), $this->personnelId(), $programConsultantId);
        $service->execute($programConsultantCompositionId, $consultantCommentId);
        return $this->commandOkResponse();
    }

    protected function buildSubmitNewService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment::class);
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new ConsultantCommentSubmitNew(
                $consultantCommentRepository, $programConsultantRepository, $worksheetRepository, $this->getDispatcher());
    }

    protected function buildSubmitReplyService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment::class);
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $commentRepository = $this->em->getRepository(Comment::class);
        return new ConsultantCommentSubmitReply(
                $consultantCommentRepository, $programConsultantRepository, $commentRepository, $this->getDispatcher());
    }

    protected function arrayDataOfConsultantComment(ConsultantComment2 $consultantComment): array
    {
        $parentData = empty($consultantComment->getParent()) ? null : [
            "id" => $consultantComment->getParent()->getId(),
            "message" => $consultantComment->getParent()->getMessage(),
        ];

        return [
            "id" => $consultantComment->getId(),
            "message" => $consultantComment->getMessage(),
            "submitTime" => $consultantComment->getSubmitTimeString(),
            "parent" => $parentData,
        ];
    }

    protected function buildRemoveService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment::class);
        return new ConsultantCommentRemove($consultantCommentRepository);
    }

    protected function buildViewService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment2::class);
        return new ConsultantCommentView($consultantCommentRepository);
    }

    protected function getDispatcher()
    {
        $dispatcher = new Dispatcher();
        $commentRepository = $this->em->getRepository(CommentForClient::class);
        $clientNotificationRepository = $this->em->getRepository(ClientNotification::class);
        $listener = new ConsultantCommentOnWorksheetListener($commentRepository, $clientNotificationRepository);
        $dispatcher->addListener(ConsultantCommentOnWorksheetEvent::EVENT_NAME, $listener);
        return $dispatcher;
    }

}
