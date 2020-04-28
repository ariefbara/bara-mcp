<?php

namespace Client\Domain\Model\Client\ProgramParticipation;

use Client\Domain\Model\Client\{
    ProgramParticipation,
    ProgramParticipation\Worksheet\Comment
};

class ParticipantComment
{

    /**
     *
     * @var ProgramParticipation
     */
    protected $programParticipation;

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

    function getProgramParticipation(): ProgramParticipation
    {
        return $this->programParticipation;
    }

    function getId(): string
    {
        return $this->id;
    }

    function __construct(ProgramParticipation $programParticipation, string $id, Comment $comment)
    {
        $this->programParticipation = $programParticipation;
        $this->id = $id;
        $this->comment = $comment;
    }

    public function remove(): void
    {
        $this->comment->remove();
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
