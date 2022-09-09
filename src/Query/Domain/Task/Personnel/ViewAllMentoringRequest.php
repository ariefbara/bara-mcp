<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequestRepository;

class ViewAllMentoringRequest implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var MentoringRequestRepository
     */
    protected $mentoringRequestRepository;

    /**
     * 
     * @var ViewAllMentoringRequestPayload
     */
    protected $viewAllMentoringRequestPayload;

    public function __construct(MentoringRequestRepository $mentoringRequestRepository,
            ViewAllMentoringRequestPayload $viewAllMentoringRequestPayload)
    {
        $this->mentoringRequestRepository = $mentoringRequestRepository;
        $this->viewAllMentoringRequestPayload = $viewAllMentoringRequestPayload;
    }

    public function execute(string $personnelId): void
    {
        $this->viewAllMentoringRequestPayload->result = $this->mentoringRequestRepository->allMentoringRequestBelongsToPersonnel(
                $personnelId, $this->viewAllMentoringRequestPayload->getPage(),
                $this->viewAllMentoringRequestPayload->getPageSize(),
                $this->viewAllMentoringRequestPayload->getMentoringRequestSearch()
        );
    }

}
