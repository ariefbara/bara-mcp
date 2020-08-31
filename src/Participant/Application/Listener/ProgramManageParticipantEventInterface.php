<?php

namespace User\Application\Listener;

use Resources\Application\Event\Event;

interface ProgramManageParticipantEventInterface extends Event
{

    public function getFirmId(): string;

    public function getProgramId(): string;

    public function getParticipantId(): string;

    public function getMessageForUser(): string;
}
