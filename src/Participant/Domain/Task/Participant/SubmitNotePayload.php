<?php

namespace Participant\Domain\Task\Participant;

class SubmitNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $content;
    public $submittedNoteId;

    public function __construct(?string $content)
    {
        $this->content = $content;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

}
