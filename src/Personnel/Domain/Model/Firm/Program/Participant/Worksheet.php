<?php

namespace Personnel\Domain\Model\Firm\Program\Participant;

use User\Domain\Model\User\Participant;

class Worksheet
{

    /**
     *
     * @var Participant
     */
    protected $participant;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $removed;

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

}
