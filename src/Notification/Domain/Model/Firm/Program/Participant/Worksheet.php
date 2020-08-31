<?php

namespace Notification\Domain\Model\Firm\Program\Participant;

use Notification\Domain\Model\Firm\Program\Participant;
use Query\Domain\Model\FirmWhitelableInfo;

class Worksheet
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    public function getFirmWhitelableInfo(): FirmWhitelableInfo
    {
        return $this->participant->getFirmWhitelableInfo();
    }

    public function getParticipantName(): string
    {
        return $this->participant->getName();
    }

    public function getProgramId(): string
    {
        return $this->participant->getProgramId();
    }

    public function getParticipantId(): string
    {
        return $this->participant->getId();
    }

}
