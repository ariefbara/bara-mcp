<?php

namespace Team\Domain\Model;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Resources\DateTimeImmutableBuilder;
use Resources\Domain\Model\EntityContainEvents;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;
use Team\Domain\DependencyModel\Firm\Client;
use Team\Domain\Event\TeamHasAppliedToProgram;
use Team\Domain\Model\Team\Member;

class Team extends EntityContainEvents
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

    /**
     * 
     * @var ArrayCollection
     */
    protected $members;
    
    protected function setName(string $name)
    {
        $errorDetail = "bad request: team name is required";
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    function __construct(string $firmId, string $id, Client $creator, string $name, string $memberPosition)
    {
        $this->firmId = $firmId;
        $this->id = $id;
        $this->setName($name);
        $this->creator = $creator;
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();

        $this->members = new ArrayCollection();
        $memberId = Uuid::generateUuid4();
        $member = new Member($this, $memberId, $creator, $anAdmin = true, $memberPosition);
        $this->members->add($member);
    }
    
    public function addMember(Client $client, bool $anAdmin, ?string $memberPosition): string
    {
        $member = $this->findMemberCorrespondWithClient($client);
        if (isset($member)) {
            $member->activate($anAdmin, $memberPosition);
        } else {
            $memberId = Uuid::generateUuid4();
            $member = new Member($this, $memberId, $client, $anAdmin, $memberPosition);
            $this->members->add($member);
        }
        return $member->getId();
    }
    protected function findMemberCorrespondWithClient(Client $client): ?Member
    {
        $p = function (Member $member) use ($client) {
            return $member->isCorrespondWithClient($client);
        };
        $member = $this->members->filter($p)->first();
        return empty($member)? null: $member;
    }
    
    public function applyToProgram(string $programId): void
    {
        $event = new TeamHasAppliedToProgram($this->firmId, $this->id, $programId);
        $this->recordEvent($event);
    }

}
