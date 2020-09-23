<?php

namespace Query\Domain\Model\Firm\Team;

use DateTimeImmutable;
use Query\Domain\Model\Firm\ {
    Client,
    Team
};

class Member
{

    /**
     *
     * @var Team
     */
    protected $team;

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
     * @var string||null
     */
    protected $position;

    /**
     *
     * @var bool
     */
    protected $anAdmin;

    /**
     *
     * @var bool
     */
    protected $active;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    protected function __construct()
    {
        ;
    }

    public function isAnAdmin(): bool
    {
        return $this->anAdmin;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getJoinTimeString(): string
    {
        return $this->joinTime->format("Y-m-d H:i:s");
    }

}
