<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest\NegotiatedMentoringRepository;

class ShowNegotiatedMentoringTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var NegotiatedMentoringRepository
     */
    protected $negotiatedMentoringRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var NegotiatedMentoring|null
     */
    public $result;

    public function __construct(NegotiatedMentoringRepository $negotiatedMentoringRepository, string $id)
    {
        $this->negotiatedMentoringRepository = $negotiatedMentoringRepository;
        $this->id = $id;
    }

    public function execute(string $participantId): void
    {
        $this->result = $this->negotiatedMentoringRepository
                ->aNegotiatedMentoringBelongsToParticipant($participantId, $this->id);
    }

}
