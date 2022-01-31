<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;

class CancelMentoringDeclarationTask implements ITaskExecutableByParticipant
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

    public function __construct(DeclaredMentoringRepository $declaredMentoringRepository, string $id)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->id = $id;
    }

    public function execute(\Participant\Domain\Model\Participant $participant): void
    {
        $declaredMentoring = $this->declaredMentoringRepository->ofId($this->id);
        $declaredMentoring->assertManageableByParticipant($participant);
        $declaredMentoring->cancel();
    }

}
