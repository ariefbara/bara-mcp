<?php

namespace Participant\Domain\Task\Participant;

class UpdateNotePayload
{

    /**
     * 
     * @var string|null
     */
    protected $id;

    /**
     * 
     * @var string|null
     */
    protected $content;

    public function __construct(?string $id, ?string $content)
    {
        $this->id = $id;
        $this->content = $content;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

}
