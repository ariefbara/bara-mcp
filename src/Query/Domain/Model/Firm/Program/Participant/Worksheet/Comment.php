<?php

namespace Query\Domain\Model\Firm\Program\Participant\Worksheet;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Consultant\ConsultantComment;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Query\Domain\Model\Firm\Team\Member\MemberComment;

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
    protected $parent;

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
     * @var ConsultantComment||null
     */
    protected $consultantComment = null;

    /**
     * 
     * @var MemberComment|null
     */
    protected $memberComment = null;

    public function getWorksheet(): Worksheet
    {
        return $this->worksheet;
    }

    public function getParent(): ?Comment
    {
        return $this->parent;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSubmitTimeString(): string
    {
        return $this->submitTime->format('Y-m-d H:i:s');
    }

    public function isRemoved(): bool
    {
        return $this->removed;
    }

    public function getConsultantComment(): ?ConsultantComment
    {
        return $this->consultantComment;
    }

    public function getMemberComment(): ?MemberComment
    {
        return $this->memberComment;
    }

    public function __construct()
    {
        ;
    }

}
