<?php

namespace Personnel\Domain\Task\Coordinator;

class UpdateNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $coordinatorNoteId;

    /**
     * 
     * @var string|null
     */
    protected $content;

    public function getCoordinatorNoteId(): ?string
    {
        return $this->coordinatorNoteId;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function __construct(?string $coordinatorNoteId, ?string $content)
    {
        $this->coordinatorNoteId = $coordinatorNoteId;
        $this->content = $content;
    }

}
