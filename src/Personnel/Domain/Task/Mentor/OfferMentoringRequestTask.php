<?php

namespace Personnel\Domain\Task\Mentor;

use Personnel\Domain\Model\Firm\Personnel\ITaskExecutableByMentor;
use Personnel\Domain\Model\Firm\Personnel\ProgramConsultant;
use Personnel\Domain\Task\Dependency\Firm\Personnel\Mentor\MentoringRequestRepository;

class OfferMentoringRequestTask implements ITaskExecutableByMentor
{

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

    /**
     * 
     * @var OfferMentoringRequestPayload
     */
    protected $payload;

    public function __construct(
            MentoringRequestRepository $mentoringRequestRepository, OfferMentoringRequestPayload $payload)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->payload = $payload;
    }

    public function execute(ProgramConsultant $mentor): void
    {
        $mentoringRequest = $this->mentoringRequestRepository->ofId($this->payload->getId());
        $mentoringRequest->assertBelongsToMentor($mentor);
        $mentoringRequest->offer($this->payload->getMentoringRequestData());
    }

}
