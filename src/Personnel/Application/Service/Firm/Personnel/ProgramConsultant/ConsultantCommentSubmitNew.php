<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\ {
    Application\Service\Firm\Personnel\ProgramConsultantRepository,
    Application\Service\Firm\Program\Participant\WorksheetRepository,
    Domain\Model\Firm\Personnel\ProgramConsultant\ConsultantComment,
    Domain\Model\Firm\Program\Participant\Worksheet\Comment
};
use Query\Application\Service\Firm\Personnel\PersonnelCompositionId;
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
            PersonnelCompositionId $personnelCompositionId, string $programConsultantId, string $participantId,
            string $worksheetId, string $message): string
    {
        $programConsultant = $this->programConsultantRepository->ofId($personnelCompositionId, $programConsultantId);
        $id = $this->consultantCommentRepository->nextIdentity();
        $worksheet = $this->worksheetRepository->aWorksheetInProgramsWhereConsultantInvolved(
                $personnelCompositionId, $programConsultantId, $participantId, $worksheetId);
        $comment = Comment::createNew($worksheet, $id, $message);

        $consultantComment = new ConsultantComment($programConsultant, $id, $comment);
        $this->consultantCommentRepository->add($consultantComment);
        
        $this->dispatcher->dispatch($consultantComment);
        return $id;
    }

}
