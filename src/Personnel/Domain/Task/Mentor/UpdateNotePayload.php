<?php

namespace Personnel\Domain\Task\Mentor;

class UpdateNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $consultantNoteId;

    /**
     * 
     * @var string|null
     */
    protected $content;

    public function getConsultantNoteId(): ?string
    {
        return $this->consultantNoteId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function __construct(?string $consultantNoteId, ?string $content)
    {
        $this->consultantNoteId = $consultantNoteId;
        $this->content = $content;
    }

}
