<?php

namespace User\Domain\Model\User;

class Participant
{

    /**
     *
     * @var string
     */
    protected $programId;

    /**
     *
     * @var string
     */
    protected $id;

    /**
     *
     * @var bool
     */
    protected $active;

    public function isActive(): bool
    {
        return $this->active;
    }

    protected function __construct()
    {
        ;
    }

}
