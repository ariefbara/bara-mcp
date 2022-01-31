<?php

namespace Participant\Domain\Task\Participant;

use Participant\Domain\Model\ITaskExecutableByParticipant;
use Participant\Domain\Task\Dependency\Firm\Program\Participant\DeclaredMentoringRepository;

class UpdateDeclaredMentoringTask implements ITaskExecutableByParticipant
{

    /**
     * 
     * @var DeclaredMentoringRepository
     */
    protected $declaredMentoringRepository;

    /**
     * 
     * @var UpdateDeclaredMentoringPayload
     */
    protected $payload;

    public function __construct(DeclaredMentoringRepository $declaredMentoringRepository,
            UpdateDeclaredMentoringPayload $payload)
    {
        $this->declaredMentoringRepository = $declaredMentoringRepository;
        $this->payload = $payload;
    }

    public function execute(\Participant\Domain\Model\Participant $participant): void
    {
        $declaredMentoring = $this->declaredMentoringRepository->ofId($this->payload->getId());
        $declaredMentoring->assertManageableByParticipant($participant);
        $declaredMentoring->update($this->payload->getScheduleData());
    }

}
