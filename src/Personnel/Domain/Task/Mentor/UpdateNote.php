<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultantNoteRepository;

class UpdateNote implements MentorTask
{

    /**
     * 
     * @var ConsultantNoteRepository
     */
    protected $consultantNoteRepository;

    public function __construct(ConsultantNoteRepository $consultantNoteRepository)
    {
        $this->consultantNoteRepository = $consultantNoteRepository;
    }

    /**
     * 
     * @param ProgramConsultant $mentor
     * @param UpdateNotePayload $payload
     * @return void
     */
    public function execute(ProgramConsultant $mentor, $payload): void
    {
        $consultantNote = $this->consultantNoteRepository->ofId($payload->getConsultantNoteId());
        $consultantNote->assertManageableByConsultant($mentor);
        
        $consultantNote->update($payload->getLabelData());
    }

}
