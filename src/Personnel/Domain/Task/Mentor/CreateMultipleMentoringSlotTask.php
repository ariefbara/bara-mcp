<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringSlotRepository;
use Personnel\Domain\Task\Dependency\Firm\Program\ConsultationSetupRepository;

class CreateMultipleMentoringSlotTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var MentoringSlotRepository
     */
    protected $mentoringSlotRepository;

    /**
     * 
     * @var ConsultationSetupRepository
     */
    protected $consultationSetupRepository;

    /**
     * 
     * @var CreateMultipleMentoringSlotPayload
     */
    protected $payload;

    public function __construct(MentoringSlotRepository $mentoringSlotRepository,
            ConsultationSetupRepository $consultationSetupRepository, CreateMultipleMentoringSlotPayload $payload)
    {
        $this->mentoringSlotRepository = $mentoringSlotRepository;
        $this->consultationSetupRepository = $consultationSetupRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $consultationSetup = $this->consultationSetupRepository->ofId($this->payload->getConsultationSetupId());
        foreach ($this->payload->getMentoringSlotDataCollection() as $mentoringSlotData) {
            $mentoringSlot = $mentor->createMentoringSlot(
                    $this->mentoringSlotRepository->nextIdentity(), $consultationSetup, $mentoringSlotData);
            $this->mentoringSlotRepository->add($mentoringSlot);
        }
    }

}
