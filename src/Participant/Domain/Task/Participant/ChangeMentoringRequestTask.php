<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;

class ChangeMentoringRequestTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

    /**
     * 
     * @var ChangeMentoringRequestPayload
     */
    protected $payload;

    public function __construct(MentoringRequestRepository $mentoringRequestRepository,
            ChangeMentoringRequestPayload $payload)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->payload = $payload;
    }

    public function execute(Participant $participant): void
    {
        $mentoringRequest = $this->mentoringRequestRepository->ofId($this->payload->getId());
        $mentoringRequest->assertManageableByParticipant($participant);
        $mentoringRequest->update($this->payload->getMentoringRequestData());
    }

}
