<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ViewParticipantSummaryListInCoordinatedProgram implements PersonnelTask
{
    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;
}
