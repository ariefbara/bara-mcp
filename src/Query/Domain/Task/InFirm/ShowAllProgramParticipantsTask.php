<?php

namespace Query\Domain\Task\InFirm;

use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\ITaskInFirmExecutableByManager;
use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantFilter;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ShowAllProgramParticipantsTask implements ITaskInFirmExecutableByManager
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    /**
     * 
     * @var ParticipantFilter
     */
    protected $filter;

    /**
     * 
     * @var Participant[]
     */
    public $results;

    public function __construct(ParticipantRepository $participantRepository, ParticipantFilter $filter)
    {
        $this->participantRepository = $participantRepository;
        $this->filter = $filter;
    }

    public function executeTaskInFirm(Firm $firm): void
    {
        $this->results = $this->participantRepository->allProgramParticipantsInFirm($firm->getId(), $this->filter);
    }

}
