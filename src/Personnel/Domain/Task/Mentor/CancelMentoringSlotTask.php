<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlotRepository;
use Resources\Exception\RegularException;

class CancelMentoringSlotTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;

    /**
     * 
     * @var string
     */
    protected $mentoringSlotId;

    public function __construct(MentoringSlotRepository $mentoringSlotRepository, string $mentoringSlotId)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->mentoringSlotId = $mentoringSlotId;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $mentoringSlot = $this->mentoringSlotRepository->ofId($this->mentoringSlotId);
        if (!$mentoringSlot->belongsToMentor($mentor)) {
            throw RegularException::forbidden('forbidden: can only manage owned mentoring slot');
        }
        $mentoringSlot->cancel();
    }

}
