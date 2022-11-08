<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\ConsultantNoteRepository;

class RemoveNote implements MentorTask
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
     * @param string $payload consultantNoteId
     * @return void
     */
    public function execute(ProgramConsultant $mentor, $payload): void
    {
        $consultantNote = $this->consultantNoteRepository->ofId($payload);
        $consultantNote->assertManageableByConsultant($mentor);
        $consultantNote->remove();
    }

}
