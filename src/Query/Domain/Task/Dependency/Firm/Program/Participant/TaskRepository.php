<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\Participant\Task;

interface TaskRepository
{

    public function allTaskForParticipant(string $participantId, TaskListFilter $taskListFilter);

    public function allTaskInProgram(string $programId, TaskListFilter $taskListFilter);

    public function taskListInAllProgramCoordinatedByPersonnel(string $personnelId, TaskListFilterForCoordinator $filter);

    public function taskListInAllProgramConsultedByPersonnel(string $personnelId, TaskListFilterForConsultant $filter);
    
    public function aTaskBelongsToParticipant(string $participantId, string $id): Task;
    
}
