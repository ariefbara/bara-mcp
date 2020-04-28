<?php

namespace Personnel\Domain\Model\Firm\Program;

use DateTimeImmutable;
use Personnel\Domain\Model\ {
    Client,
    Firm\Program
};

class Participant
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var Client
     */
    protected $client;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $acceptedTime;

    /**
     *
     * @var bool
     */
    protected $active = true;

    /**
     *
     * @var string
     */
    protected $note;

    protected function __construct()
    {
        ;
    }
}
