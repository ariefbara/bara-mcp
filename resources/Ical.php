<?php

namespace Resources;

use DateTimeImmutable;
use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;

class Ical
{

    /**
     * 
     * @var Event
     */
    protected $event;

    /**
     * 
     * @var Calendar
     */
    protected $calendar;

    public function __construct(string $id, bool $useTimezone = true)
    {
        $this->event = new Event($id);
        $this->event->setUseTimezone($useTimezone);
        $this->calendar = new Calendar("innov.id");
        $this->calendar->addEvent($this->event);
    }

    public function render(): string
    {
        return $this->calendar->render();
    }

    public function setSummary(string $summary): self
    {
        $this->event->setSummary($summary);
        return $this;
    }

    public function setDtStart(DateTimeImmutable $startTime): self
    {
        $this->event->setDtStart($startTime);
        return $this;
    }

    public function setDtEnd(DateTimeImmutable $endTime): self
    {
        $this->event->setDtEnd($endTime);
        return $this;
    }

    public function setSequence(int $sequence): self
    {
        $this->event->setSequence($sequence);
        return $this;
    }

    public function setCancelled(): self
    {
        $this->event->setCancelled(true);
        return $this;
    }

}
