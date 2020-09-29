<?php

namespace Query\Domain\Model\Firm\Team;

use DateTimeImmutable;
use Query\Domain\ {
    Model\Firm\Client,
    Model\Firm\Team,
    Service\Firm\ClientFinder
};
use Resources\Exception\RegularException;

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
    
    public function viewClientByEmail(ClientFinder $clientFinder, string $clientEmail): Client
    {
        $this->assertAnAdmin();
        $this->assertActive();
        return $clientFinder->findByEmail($this->client->getFirm()->getId(), $clientEmail);
    }
    
    protected function assertAnAdmin(): void
    {
        if (!$this->anAdmin) {
            $errorDetail = "forbidden: only team admin can make this requests";
            throw RegularException::forbidden($errorDetail);
        }
    }
    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this requests";
            throw RegularException::forbidden($errorDetail);
        }
        
    }

}
