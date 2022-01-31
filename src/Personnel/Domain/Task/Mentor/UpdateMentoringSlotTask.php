<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlotRepository;
use Resources\Exception\RegularException;

class UpdateMentoringSlotTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;

    /**
     * 
     * @var UpdateMentoringSlotPayload
     */
    protected $payload;

    public function __construct(MentoringSlotRepository $mentoringSlotRepository, UpdateMentoringSlotPayload $payload)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $mentoringSlot = $this->mentoringSlotRepository->ofId($this->payload->getId());
        if (!$mentoringSlot->belongsToMentor($mentor)) {
            throw RegularException::forbidden('forbidden: can only manage owned mentoring slot');
        }
        $mentoringSlot->update($this->payload->getMentoringSlotData());
    }

}
