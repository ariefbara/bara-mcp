<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantFilter;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ViewAllManageableParticipant implements TaskExecutableByPersonnel
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
    protected $participantFilter;
    /**
     * 
     * @var Participant[]
     */
    public $result;

    public function __construct(ParticipantRepository $participantRepository, ParticipantFilter $participantFilter)
    {
        $this->participantRepository = $participantRepository;
        $this->participantFilter = $participantFilter;
    }

    public function execute(string $personnelId): void
    {
        $this->result = $this->participantRepository
                ->allProgramParticipantsManageableByPersonnel($personnelId, $this->participantFilter);
    }

}
