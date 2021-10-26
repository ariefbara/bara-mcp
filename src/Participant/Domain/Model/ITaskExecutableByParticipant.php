<?php

namespace Participant\Domain\Model;

interface ITaskExecutableByParticipant
{
    public function execute(Participant $participant): void;
}
