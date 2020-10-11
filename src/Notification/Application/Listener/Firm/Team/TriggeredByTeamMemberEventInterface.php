<?php

namespace Notification\Application\Listener\Firm\Team;

use Resources\Application\Event\Event;

interface TriggeredByTeamMemberEventInterface extends Event
{

    public function getMemberId(): string;

    public function getId(): string;
}
