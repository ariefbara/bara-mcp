<?php

namespace Query\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\ {
    ArrayCollection,
    Criteria
};
use Query\Domain\Model\ {
    Firm,
    Firm\Program\Consultant,
    Firm\Program\Coordinator
};
use Resources\Domain\ValueObject\ {
    Password,
    PersonName
};

class Personnel
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
     * @var PersonName
     */
    protected $name;

    /**
     *
     * @var string
     */
    protected $email;

    /**
     *
     * @var Password
     */
    protected $password;

    /**
     *
     * @var string||null
     */
    protected $phone;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    /**
     *
     * @var bool
     */
    protected $removed;

    /**
     *
     * @var ArrayCollection
     */
    protected $programCoordinators;

    /**
     *
     * @var ArrayCollection
     */
    protected $programConsultants;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getEmail(): string
    {
        return $this->email;
    }

    function getPhone(): ?string
    {
        return $this->phone;
    }

    function getJoinTimeString(): string
    {
        return $this->joinTime->format('Y-m-d H:i:s');
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    /**
     * 
     * @return Coordinator[]
     */
    function getUnremovedProgramCoordinators()
    {
        return $this->programCoordinators->matching($this->unremovedCriteria())->getIterator();
    }

    /**
     * 
     * @return Consultant[]
     */
    function getUnremovedProgramConsultants()
    {
        return $this->programConsultants->matching($this->unremovedCriteria())->getIterator();
    }

    protected function __construct()
    {
        ;
    }

    public function passwordMatches(string $password): bool
    {
        return $this->password->match($password);
    }

    private function unremovedCriteria()
    {
        return Criteria::create()
                        ->andWhere(Criteria::expr()->eq('removed', false));
    }
    
    public function getName(): string
    {
        return $this->name->getFullName();
    }

}
