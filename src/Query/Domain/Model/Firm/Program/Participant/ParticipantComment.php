<?php

namespace Query\Domain\Model\Firm\Program\Participant;

use Query\Domain\Model\Firm\Program\{
    Participant,
    Participant\Worksheet\Comment
};

class ParticipantComment
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
     * @var Comment
     */
    protected $comment;

    function getParticipant(): Participant
    {
        return $this->participant;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

    function getParent(): ?Comment
    {
        return $this->comment->getParent();
    }

    function getMessage(): ?string
    {
        return $this->comment->getMessage();
    }

    function getSubmitTimeString(): string
    {
        return $this->comment->getSubmitTimeString();
    }

    function isRemoved(): bool
    {
        return $this->comment->isRemoved();
    }

}
