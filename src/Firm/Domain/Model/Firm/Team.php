<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Team\Member;
use Firm\Domain\Model\Firm\Team\MemberData;
use Resources\DateTimeImmutableBuilder;
use Resources\Exception\RegularException;
use Resources\Uuid;
use Resources\ValidationRule;
use Resources\ValidationService;

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
     * @var DateTimeImmutable
     */
    protected $createdTime;
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $members;

    function getId(): string
    {
        return $this->id;
    }

    protected function setName(string $name): void
    {
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, 'bad request: team name is mandatory');
        $this->name = $name;
    }
    public function __construct(Firm $firm, string $id, TeamData $teamData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->setName($teamData->getName());
        $this->createdTime = DateTimeImmutableBuilder::buildYmdHisAccuracy();
        
        $this->members = new ArrayCollection();
        foreach ($teamData->getMemberDataList() as $memberData) {
            $member = new Member($this, Uuid::generateUuid4(), $memberData);
            $this->members->add($member);
        }
        if (empty($this->members->count())) {
            throw RegularException::forbidden('forbidden: team must have at least one member');
        }
    }
    
    public function assertManageableInFirm(Firm $firm): void
    {
        if ($this->firm !== $firm) {
            throw RegularException::forbidden('forbidden: can only manage team in same firm');
        }
    }

    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }
    
    public function addMember(MemberData $memberData): string
    {
        $p = function(Member $member) use($memberData) {
            return $member->correspondWithClient($memberData->getClient());
        };
        $member = $this->members->filter($p)->first();
        if (!empty($member)) {
            $member->enable($memberData->getPosition());
        } else {
            $member = new Member($this, Uuid::generateUuid4(), $memberData);
            $this->members->add($member);
        }
        return $member->getId();
    }
    
    public function disableMember(string $memberId): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('id', $memberId));
        $member = $this->members->matching($criteria)->first();
        if (empty($member)) {
            throw RegularException::notFound('not found: team member not found');
        }
        $member->disable();
    }

}
