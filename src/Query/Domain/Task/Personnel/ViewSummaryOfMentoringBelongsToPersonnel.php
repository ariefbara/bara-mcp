<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\MentoringRepository;
use Query\Domain\Task\GenericQueryPayload;

class ViewSummaryOfMentoringBelongsToPersonnel implements PersonnelTask
{

    /**
     * 
     * @var MentoringRepository
     */
    protected $mentoringRepository;

    public function __construct(MentoringRepository $mentoringRepository)
    {
        $this->mentoringRepository = $mentoringRepository;
    }

    /**
     * 
     * @param string $personnelId
     * @param GenericQueryPayload $payload
     * @return void
     */
    public function execute(string $personnelId, $payload): void
    {
        $payload->result = $this->mentoringRepository->summaryOfMentoringBelongsToPersonnel($personnelId);
    }

}
