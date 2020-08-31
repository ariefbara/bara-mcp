<?php

namespace Participant\Domain\Model\Participant\Worksheet;

use DateTimeImmutable;
use Participant\Domain\Model\ {
    DependencyEntity\Firm\Program\Consultant\ConsultantComment,
    Participant\Worksheet
};
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
    protected $message;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $submitTime;

    /**
     *
     * @var bool
     */
    protected $removed;
    
    /**
     *
     * @var ConsultantComment||null
     */
    protected $consultantComment;

    public function __construct(Worksheet $worksheet, string $id, string $message)
    {
        $this->worksheet = $worksheet;
        $this->parent = null;
        $this->id = $id;
        $this->message = $message;
        $this->submitTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        $this->removed = false;
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
