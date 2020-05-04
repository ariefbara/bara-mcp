<?php

namespace Client\Domain\Model\Client\ProgramParticipation\Worksheet;

use Client\Domain\Model\Client\ {
    ClientNotification,
    ProgramParticipation\Worksheet
};
use DateTimeImmutable;

class Comment
{

    /**
     *
     * @var Worksheet
     */
    protected $worksheet;

    /**
     *
     * @var Comment
     */
    protected $parent = null;

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

    function getId(): string
    {
        return $this->id;
    }

    function getWorksheet(): Worksheet
    {
        return $this->worksheet;
    }

    function getParent(): ?Comment
    {
        return $this->parent;
    }

    function getMessage(): ?string
    {
        return $this->message;
    }

    function getSubmitTimeString(): string
    {
        return $this->submitTime->format("Y-m-d H:i:s");
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    protected function __construct(Worksheet $worksheet, string $id, string $message)
    {
        $this->worksheet = $worksheet;
        $this->parent = null;
        $this->id = $id;
        $this->message = $message;
        $this->submitTime = new DateTimeImmutable();
        $this->removed = false;
    }

    public static function createNew(Worksheet $worksheet, string $id, string $message): self
    {
        return new static($worksheet, $id, $message);
    }

    public function createReply(string $id, string $message): self
    {
        $reply = new static($this->worksheet, $id, $message);
        $reply->parent = $this;
        return $reply;
    }

    public function remove(): void
    {
        $this->removed = true;
    }
    
    public function createClientNotification(string $id, string $message): ClientNotification
    {
        return $this->worksheet->createClientNotification($id, $message, $this);
    }

}
