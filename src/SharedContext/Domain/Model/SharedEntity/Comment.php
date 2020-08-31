<?php

namespace SharedContext\Domain\Model\SharedEntity;

use DateTimeImmutable;
use Resources\DateTimeImmutableBuilder;

class Comment
{

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

    public function __construct(string $id, string $message)
    {
        $this->parent = null;
        $this->id = $id;
        $this->message = $message;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->removed = false;
    }

    public function createReply(string $id, string $message): self
    {
        $reply = new static($id, $message);
        $reply->parent = $this;
        return $reply;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
