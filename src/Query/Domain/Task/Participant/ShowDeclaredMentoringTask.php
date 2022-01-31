<?php

namespace Query\Domain\Task\Participant;

use Query\Domain\Model\Firm\Program\ITaskExecutableByParticipant;
use Query\Domain\Model\Firm\Program\Participant\DeclaredMentoring;
use Query\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;

class ShowDeclaredMentoringTask implements ITaskExecutableByParticipant
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

    public function execute(string $participantId): void
    {
        $this->result = $this->declaredMentoringRepository
                ->aDeclaredMentoringBelongsToParticipant($participantId, $this->id);
    }

}
