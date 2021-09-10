<?php

namespace Query\Domain\Model\Firm\Program;

interface ITaskExecutableByParticipant
{
    public function execute(string $participantId): void;
}
