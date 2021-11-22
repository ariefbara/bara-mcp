<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ShowProgramParticipantTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;
    protected $id;

    /**
     * 
     * @var Participant|null
     */
    public $result;

    public function __construct(ParticipantRepository $participantRepository, $id)
    {
        $this->participantRepository = $participantRepository;
        $this->id = $id;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->result = $this->participantRepository->aProgramParticipantInFirm($firm->getId(), $this->id);
    }

}
