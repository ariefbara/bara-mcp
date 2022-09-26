<?php

namespace Query\Application\Service\Personnel\DedicatedMentor;

use Query\Domain\Model\Firm\Program\Participant\QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor;

class ExecuteQueryTaskOnDedicatedMentee
{

    /**
     * 
     * @var DedicatedMentorRepository
     */
    protected $dedicatedMentorRepository;

    public function __construct(DedicatedMentorRepository $dedicatedMentorRepository)
    {
        $this->dedicatedMentorRepository = $dedicatedMentorRepository;
    }

    public function execute(
            string $personnelId, string $dedicatedMentorId, QueryTaskOnDedicatedMenteeExecutableByDedicatedMentor $task,
            $payload): void
    {
        $this->dedicatedMentorRepository->aDedicatedMentorOfPersonnel($personnelId, $dedicatedMentorId)
                ->executeQueryTaskOnDedicatedMentee($task, $payload);
    }

}
