<?php

namespace Query\Domain\Model\Firm\Program\Consultant;

use Query\Domain\Model\Firm\Program\{
    Consultant,
    Participant\Worksheet,
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

    public function getId(): string
    {
        return $this->id;
    }

    public function getConsultant(): Consultant
    {
        return $this->consultant;
    }

    public function __construct()
    {
        ;
    }

    public function getWorksheet(): Worksheet
    {
        return $this->comment->getWorksheet();
    }

    public function getMessage(): string
    {
        return $this->comment->getMessage();
    }

    public function getSubmitTimeString(): string
    {
        return $this->comment->getSubmitTimeString();
    }

    public function isRemoved(): bool
    {
        return $this->comment->isRemoved();
    }

}
