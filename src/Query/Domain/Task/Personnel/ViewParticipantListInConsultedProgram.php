<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\CommonViewListPayload;
use Query\Domain\Task\Dependency\Firm\Program\ParticipantRepository;

class ViewParticipantListInConsultedProgram implements PersonnelTask
{

    /**
     * 
     * @var ParticipantRepository
     */
    protected $participantRepository;

    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    /**
     * 
     * @param string $personnelId
     * @param CommonViewListPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->participantRepository
                ->listOfParticipantInAllProgramConsultedByPersonnel($personnelId, $payload->getFilter());
    }

}
