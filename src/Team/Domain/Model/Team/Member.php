<?php

namespace Team\Domain\Model\Team;

use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Exception\RegularException
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use Team\Domain\ {
    DependencyModel\Firm\Client,
    Model\Team
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

    public function getId(): string
    {
        return $this->id;
    }

    public function __construct(Team $team, string $id, Client $client, bool $anAdmin, ?string $position)
    {
        $this->team = $team;
        $this->id = $id;
        $this->client = $client;
        $this->anAdmin = $anAdmin;
        $this->position = $position;
        $this->active = true;
        $this->joinTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
    }

    public function addTeamMember(Client $client, bool $anAdmin, ?string $memberPosition): string
    {
        $this->assertActive();
        $this->assertAnAdmin();
        return $this->team->addMember($client, $anAdmin, $memberPosition);
    }

    public function removeOtherMember(Member $other): void
    {
        $this->assertActive();
        $this->assertAnAdmin();
        $other->remove();
    }

    protected function assertAnAdmin(): void
    {
        if (!$this->anAdmin) {
            $errorDetail = "forbidden: only team member with admin priviledge can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

    public function remove(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: member already inactive";
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = false;
    }

    public function activate(bool $anAdmin, ?string $position): void
    {
        if ($this->active) {
            $errorDetail = "forbidden: member already active";
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = true;
        $this->anAdmin = $anAdmin;
        $this->position = $position;
    }

    public function isCorrespondWithClient(Client $client): bool
    {
        return $this->client === $client;
    }
    
    public function uploadFile(string $teamFileInfoId, FileInfoData $fileInfoData): TeamFileInfo
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active team member can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        return new TeamFileInfo($this->team, $teamFileInfoId, $fileInfoData);
    }

}
