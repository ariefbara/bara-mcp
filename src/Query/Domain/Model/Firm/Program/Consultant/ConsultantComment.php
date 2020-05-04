<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\ {
    Consultant,
    Participant\Worksheet\Comment
};

class ConsultantComment
{

    /**
     *
     * @var Consultant
     */
    protected $consultant;

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

    function getConsultant(): Consultant
    {
        return $this->consultant;
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
