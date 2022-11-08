<?php

namespace Personnel\Domain\Task\Mentor;

class SubmitNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $participantId;

    /**
     * 
     * @var string|null
     */
    protected $content;

    /**
     * 
     * @var bool|null
     */
    protected $viewableByParticipant;
    public $submittedNoteId;

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getViewableByParticipant(): ?bool
    {
        return $this->viewableByParticipant;
    }

    public function __construct(?string $participantId, ?string $content, ?bool $viewableByParticipant)
    {
        $this->participantId = $participantId;
        $this->content = $content;
        $this->viewableByParticipant = $viewableByParticipant;
    }

}
