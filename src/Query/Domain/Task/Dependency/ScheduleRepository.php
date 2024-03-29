<?php

namespace Query\Domain\Task\Dependency;

interface ScheduleRepository
{

    public function allScheduleBelongsToClient(string $clientId, ScheduleFilter $filter): array;
    
    public function allScheduleBelongsToParticipant(string $participantId, ScheduleFilter $filter): array;
    
    public function allScheduleInProgram(string $programId, ScheduleFilter $filter): array ;
    
    public function allScheduleBelongsToPersonnel(string $personnelId, ScheduleFilter $filter): array;
}
