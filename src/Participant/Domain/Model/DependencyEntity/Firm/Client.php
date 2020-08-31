<?php

namespace Participant\Domain\Model\DependencyEntity\Firm;

class Client
{

    /**
     *
     * @var string
     */
    protected $firmId;

    /**
     *
     * @var string
     */
    protected $id;

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    protected function __construct()
    {
        ;
    }

}
