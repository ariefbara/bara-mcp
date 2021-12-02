<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;
use Query\Domain\Model\Firm\TaskExecutableByPersonnel;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;

class ShowDeclaredMentoringTask implements TaskExecutableByPersonnel
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var DeclaredMentoring|null
     */
    public $result;

    public function __construct(DeclaredMentoringRepository $declaredMentoringRepository, string $id)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->id = $id;
    }

    public function execute(string $personnelId): void
    {
        $this->result = $this->declaredMentoringRepository
                ->aDeclaredMentoringBelongsToPersonnel($personnelId, $this->id);
    }

}
