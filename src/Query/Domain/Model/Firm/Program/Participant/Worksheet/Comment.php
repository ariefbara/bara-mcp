<?php

namespace Query\Domain\Model\Firm\Program\Participant\Worksheet;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\{
    Consultant\ConsultantComment,
    Participant\ParticipantComment,
    Participant\Worksheet
};

class Comment
{

    /**
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     *
     * @var Comment||null
     */
    protected $parent = null;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var string
     */
    protected $message = null;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    /**
     *
     * @var ParticipantComment||null
     */
    protected $participantComment = null;

    /**
     *
     * @var ConsultantComment||null
     */
    protected $consultantComment = null;

    function getWorksheet(): Worksheet
    {
        return $this->worksheet;
    }

    function getParent(): ?Comment
    {
        return $this->parent;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getMessage(): string
    {
        return $this->message;
    }

    function getSubmitTimeString(): string
    {
        return $this->submitTime->format("Y-m-d H:i:s");
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    function getParticipantComment(): ?ParticipantComment
    {
        return $this->participantComment;
    }

    function getConsultantComment(): ?ConsultantComment
    {
        return $this->consultantComment;
    }

    public function __construct()
    {
        ;
    }

}
