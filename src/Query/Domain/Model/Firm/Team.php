<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Client;
use Query\Domain\Model\Firm\Team\Member;

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
    
    /**
     * 
     * @var ArrayCollection
     */
    protected $members;

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
    
    /**
     * 
     * @return Member[]
     */
    public function iterateActiveMember()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('active', true));
        return $this->members->matching($criteria)->getIterator();
    }
    
    public function hasActiveMemberCorrespondWithClient(Client $client): bool
    {
        $p = function (Member $member) use ($client) {
            return $member->isActiveMemberCorrespondWithClient($client);
        };
        return !empty($this->members->filter($p)->count());
        return false;
    }
    
    public function getListOfActiveMemberPlusTeamName(): array
    {
        $result = [];
        foreach ($this->iterateActiveMember() as $member) {
            $result[] = "{$member->getClientName()} (of team: $this->name)";
        }
        return $result;
    }

}
