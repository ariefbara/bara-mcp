<?php

namespace Personnel\Application\Service\Firm\Personnel\ProgramConsultant;

use Personnel\Application\Service\Firm\Personnel\ProgramConsultantRepository;
use Personnel\Domain\Task\Mentor\MentorTask;

class ExecuteMentorTask
{

    /**
     * 
     * @var ProgramConsultantRepository
     */
    protected $mentorRepository;

    public function __construct(ProgramConsultantRepository $mentorRepository)
    {
        $this->mentorRepository = $mentorRepository;
    }

    public function execute(string $firmId, string $personnelId, string $mentorId, MentorTask $task, $payload): void
    {
        $this->mentorRepository->ofId($firmId, $personnelId, $mentorId)
                ->executeMentorTask($task, $payload);
        $this->mentorRepository->update();
    }

}
