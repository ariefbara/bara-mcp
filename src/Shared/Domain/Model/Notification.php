<?php

namespace Shared\Domain\Model;

use DateTimeImmutable;

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
            
    function __construct(string $id, string $message)
    {
        $this->id = $id;
        $this->message = $message;
        $this->read = false;
        $this->notifiedTime = new DateTimeImmutable();
    }
    
    public function read(): void
    {
        $this->read = true;
    }


}
