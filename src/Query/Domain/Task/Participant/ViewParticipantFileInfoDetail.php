<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\Participant;
use Query\Domain\Task\CommonViewDetailPayload;
use Query\Domain\Task\Dependency\Firm\Program\Participant\ParticipantFileInfoRepository;

class ViewParticipantFileInfoDetail implements ParticipantQueryTask
{

    /**
     * 
     * @var ParticipantFileInfoRepository
     */
    protected $participantFileInfoRepository;

    public function __construct(ParticipantFileInfoRepository $participantFileInfoRepository)
    {
        $this->participantFileInfoRepository = $participantFileInfoRepository;
    }

    /**
     * 
     * @param Participant $participant
     * @param CommonViewDetailPayload $payload
     * @return void
     */
    public function execute(Participant $participant, $payload): void
    {
        $payload->result = $this->participantFileInfoRepository
                ->aParticipantFileInfoBelongsToParticipant($participant->getId(), $payload->getId());
    }

}
