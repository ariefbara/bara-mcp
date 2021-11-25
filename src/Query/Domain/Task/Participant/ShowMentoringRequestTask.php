<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;

class ShowMentoringRequestTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var MentoringRequest|null
     */
    public $result;

    public function __construct(MentoringRequestRepository $mentoringRequestRepository, string $id)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->id = $id;
    }

    public function execute(string $participantId): void
    {
        $this->result = $this->mentoringRequestRepository
                ->aMentoringRequestBelongsToParticipant($participantId, $this->id);
    }

}
