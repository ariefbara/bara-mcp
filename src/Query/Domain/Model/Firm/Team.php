<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Query\Domain\Model\ {
    Firm,
    Firm\Client
};

class Team
{

    /**
     *
     * @var Firm
     */
    protected $firm;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var string
     */
    protected $name;

    /**
     * 
     * @var Client
     */
    protected $creator;

    /**
     * 
     * @var DateTimeImmutable
     */
    protected $createdTime;

    public function getFirm(): Firm
    {
        return $this->firm;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCreator(): Client
    {
        return $this->creator;
    }

    protected function __construct()
    {
        ;
    }

    public function getCreatedTimeString(): string
    {
        return $this->createdTime->format("Y-m-d H:i:s");
    }

}
