<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;

class ShowMentoringRequestTask implements TaskExecutableByPersonnel
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

    public function execute(string $personnelId): void
    {
        $this->result = $this->mentoringRequestRepository->aMentoringRequestBelongsToPersonnel($personnelId, $this->id);
    }

}
