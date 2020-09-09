<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\ {
    Personnel\ProgramConsultantRepository,
    Program\Participant\WorksheetRepository
};
use Resources\Application\Event\Dispatcher;

class ConsultantCommentSubmitNew
{

    /**
     *
     * @var ConsultantCommentRepository
     */
    protected $consultantCommentRepository;

    /**
     *
     * @var ProgramConsultantRepository
     */
    protected $programConsultantRepository;

    /**
     *
     * @var WorksheetRepository
     */
    protected $worksheetRepository;

    /**
     *
     * @var Dispatcher
     */
    protected $dispatcher;

    function __construct(
            ConsultantCommentRepository $consultantCommentRepository,
            ProgramConsultantRepository $programConsultantRepository, WorksheetRepository $worksheetRepository,
            Dispatcher $dispatcher)
    {
        $this->consultantCommentRepository = $consultantCommentRepository;
        $this->programConsultantRepository = $programConsultantRepository;
        $this->worksheetRepository = $worksheetRepository;
        $this->dispatcher = $dispatcher;
    }

    public function execute(
            string $firmId, string $personnelId, string $programConsultationId, string $participantId,
            string $worksheetId, string $message): string
    {
        $id = $this->consultantCommentRepository->nextIdentity();
        $worksheet = $this->worksheetRepository->aWorksheetInProgramsWhereConsultantInvolved(
                $firmId, $personnelId, $programConsultationId, $participantId, $worksheetId);

        $programConsultation = $this->programConsultantRepository->ofId($firmId, $personnelId, $programConsultationId);
        $consultantComment = $programConsultation->submitNewCommentOnWorksheet($id, $worksheet, $message);
        $this->consultantCommentRepository->add($consultantComment);
        
        $this->dispatcher->dispatch($programConsultation);
        
        return $id;
    }

}
