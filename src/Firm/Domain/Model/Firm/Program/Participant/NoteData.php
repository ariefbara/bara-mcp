<?php

namespace Firm\Domain\Model\Firm\Program\Participant;

class NoteData
{

    /**
     * 
     * @var string|null
     */
    protected $contents;

    /**
     * 
     * @var bool|null
     */
    protected $visibleToParticipant;

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function getVisibleToParticipant(): ?bool
    {
        return $this->visibleToParticipant;
    }

    public function __construct(?string $contents, ?bool $visibleToParticipant)
    {
        $this->contents = $contents;
        $this->visibleToParticipant = $visibleToParticipant;
    }

}
