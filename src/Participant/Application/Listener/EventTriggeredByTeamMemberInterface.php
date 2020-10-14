<?php

namespace Participant\Application\Listener;

use Resources\Application\Event\Event;

interface EventTriggeredByTeamMemberInterface extends Event
{

    public function getTeamMemberId(): string;

    public function getParticipantId(): string;

    public function getId(): string;
}
