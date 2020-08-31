<?php

namespace Firm\Domain\Model\SharedEntity;

class Notification
{

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
     * @var bool
     */
    protected $read = false;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $notifiedTime;

    public function __construct(string $id, string $message)
    {
//        $this->id = $id;
//        $this->message = $message;
//        $this->read = $read;
//        $this->notifiedTime = $notifiedTime;
    }

}
