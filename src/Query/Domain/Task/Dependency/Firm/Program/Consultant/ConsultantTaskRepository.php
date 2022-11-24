<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\Consultant\ConsultantTask;

interface ConsultantTaskRepository
{

    public function aConsultantTaskDetailForParticipant(string $participantId, string $id): ConsultantTask;

    public function aConsultantTaskDetailInProgram(string $programId, string $id): ConsultantTask;
}
