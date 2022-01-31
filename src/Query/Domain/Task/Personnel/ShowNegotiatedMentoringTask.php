<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Participant\MentoringRequest\NegotiatedMentoring;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Participant\MentoringRequest\NegotiatedMentoringRepository;

class ShowNegotiatedMentoringTask implements TaskExecutableByPersonnel
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

    public function execute(string $personnelId): void
    {
        $this->result = $this->negotiatedMentoringRepository
                ->aNegotiatedMentoringBelongsToPersonnel($personnelId, $this->id);
    }

}
