<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\MentoringRepository;

class ViewAllMentoringTask implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var MentoringRepository
     */
    protected $mentoringRepository;

    /**
     * 
     * @var ViewAllMentoringPayload
     */
    protected $payload;

    /**
     * 
     * @var array|null
     */
    public $results;

    public function __construct(MentoringRepository $mentoringRepository, ViewAllMentoringPayload $payload)
    {
        $this->mentoringRepository = $mentoringRepository;
        $this->payload = $payload;
    }

    public function execute(string $personnelId): void
    {
        $this->results = $this->mentoringRepository->allMentoringsBelongsToPersonnel(
                $personnelId, $this->payload->getPage(), $this->payload->getPageSize(),
                $this->payload->getMentoringFilter());
    }

}
