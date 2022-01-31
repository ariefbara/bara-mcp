<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Model\Participant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;

class AcceptMentoringOfferingTask implements ITaskExecutableByParticipant
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

    public function __construct(MentoringRequestRepository $mentoringRequestRepository, string $id)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->id = $id;
    }

    public function execute(Participant $participant): void
    {
        $mentoringRequest = $this->mentoringRequestRepository->ofId($this->id);
        $mentoringRequest->assertManageableByParticipant($participant);
        $mentoringRequest->accept();
    }

}
