<?php

namespace Participant\Domain\Event;

use Notification\Application\Listener\Firm\Team\TriggeredByTeamMemberEventInterface;
use Resources\Domain\Event\CommonEvent;

class EventTriggeredByTeamMember implements TriggeredByTeamMemberEventInterface
{

    /**
     *
     * @var CommonEvent
     */
    protected $event;

    /**
     *
     * @var string
     */
    protected $memberId;

    public function __construct(CommonEvent $event, string $memberId)
    {
        $this->event = $event;
        $this->memberId = $memberId;
    }

    public function getName(): string
    {
        return $this->event->getName();
    }

    public function getId(): string
    {
        return $this->event->getId();
    }

    public function getMemberId(): string
    {
        return $this->memberId;
    }

}
