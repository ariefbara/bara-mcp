<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Firm\Domain\Model\Firm;
use Firm\Domain\Model\Firm\Team\Member;
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

    public function idEquals(string $id): bool
    {
        return $this->id === $id;
    }

}
