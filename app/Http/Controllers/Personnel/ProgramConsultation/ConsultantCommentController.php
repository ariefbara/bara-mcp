<?php

namespace App\Http\Controllers\Personnel\ProgramConsultation;

use App\Http\Controllers\Personnel\PersonnelBaseController;
use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentRemove,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentSubmitNew,
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentSubmitReply,
    Domain\Model\Firm\Personnel\ProgramConsultant,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment as ConsultantComment2,
    Domain\Model\Firm\Program\Participant\Worksheet,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment as Comment2
};
use Query\ {
    Application\Service\Firm\Personnel\ProgramConsultant\ConsultantCommentView,
    Domain\Model\Firm\Program\Consultant\ConsultantComment
};
use Resources\Application\Event\Dispatcher;

class ConsultantCommentController extends PersonnelBaseController
{
    public function submitNew($programConsultantId)
    {
        $service = $this->buildSubmitNewService();

        $participantId = $this->stripTagsInputRequest('participantId');
        $worksheetId = $this->stripTagsInputRequest('worksheetId');
        $message = $this->stripTagsInputRequest('message');

        $consultantCommentId = $service->execute($this->firmId(), $this->personnelId(), $programConsultantId,
                $participantId, $worksheetId, $message);

        $viewService = $this->buildViewService();
        $consultantComment = $viewService->showById(
                $this->firmId(), $this->personnelId(), $programConsultantId, $consultantCommentId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultantComment($consultantComment));
    }

    public function submitReply($programConsultantId)
    {
        $service = $this->buildSubmitReplyService();

        $participantId = $this->stripTagsInputRequest('participantId');
        $worksheetId = $this->stripTagsInputRequest('worksheetId');
        $commentId = $this->stripTagsInputRequest('commentId');
        $message = $this->stripTagsInputRequest('message');
        
        $consultantCommentId = $service->execute(
                $this->firmId(), $this->personnelId(), $programConsultantId, $participantId, $worksheetId, $commentId,
                $message);

        $viewService = $this->buildViewService();
        $consultantComment = $viewService->showById(
                $this->firmId(), $this->personnelId(), $programConsultantId, $consultantCommentId);
        return $this->commandCreatedResponse($this->arrayDataOfConsultantComment($consultantComment));
    }

    public function remove($programConsultantId, $consultantCommentId)
    {
        $service = $this->buildRemoveService();
        $service->execute($this->firmId(), $this->personnelId(), $programConsultantId, $consultantCommentId);
        return $this->commandOkResponse();
    }

    protected function buildSubmitNewService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment2::class);
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $worksheetRepository = $this->em->getRepository(Worksheet::class);
        return new ConsultantCommentSubmitNew(
                $consultantCommentRepository, $programConsultantRepository, $worksheetRepository, $this->getDispatcher());
    }

    protected function buildSubmitReplyService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment2::class);
        $programConsultantRepository = $this->em->getRepository(ProgramConsultant::class);
        $commentRepository = $this->em->getRepository(Comment2::class);
        return new ConsultantCommentSubmitReply(
                $consultantCommentRepository, $programConsultantRepository, $commentRepository, $this->getDispatcher());
    }

    protected function arrayDataOfConsultantComment(ConsultantComment $consultantComment): array
    {
        return [
            "id" => $consultantComment->getId(),
            "message" => $consultantComment->getMessage(),
            "submitTime" => $consultantComment->getSubmitTimeString(),
            "removed" => $consultantComment->isRemoved(),
            "participant" => null,
        ];
    }

    protected function buildRemoveService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment2::class);
        return new ConsultantCommentRemove($consultantCommentRepository);
    }

    protected function buildViewService()
    {
        $consultantCommentRepository = $this->em->getRepository(ConsultantComment::class);
        return new ConsultantCommentView($consultantCommentRepository);
    }

    protected function getDispatcher()
    {
        $dispatcher = new Dispatcher();
        return $dispatcher;
    }

}
