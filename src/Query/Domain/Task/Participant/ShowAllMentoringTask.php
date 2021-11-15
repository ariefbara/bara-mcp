<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Task\Dependency\MentoringRepository;

class ShowAllMentoringTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var MentoringRepository
     */
    protected $mentoringRepository;

    /**
     * 
     * @var ShowAllMentoringPayload
     */
    protected $payload;

    /**
     * 
     * @var array|null
     */
    public $results;

    public function __construct(MentoringRepository $mentoringRepository, ShowAllMentoringPayload $payload)
    {
        $this->mentoringRepository = $mentoringRepository;
        $this->payload = $payload;
    }

    public function execute(string $participantId): void
    {
        $this->results = $this->mentoringRepository->allMentoringsBelongsToParticipant(
                $participantId, $this->payload->getPage(), $this->payload->getPageSize(), $this->payload->getFilter());
    }

}
