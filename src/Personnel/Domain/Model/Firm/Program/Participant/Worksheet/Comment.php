<?php

namespace Personnel\Domain\Model\Firm\Program\Participant\Worksheet;

use DateTimeImmutable;
use Query\Domain\Model\Firm\Program\Participant\Worksheet;
use Resources\DateTimeImmutableBuilder;

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

    protected function __construct(Worksheet $worksheet, string $id, string $message)
    {
        $this->worksheet = $worksheet;
        $this->parent = null;
        $this->id = $id;
        $this->message = $message;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
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

}
