<?php

namespace Query\Domain\Model\Firm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Query\Domain\Model\Firm;
use Query\Domain\Model\Firm\Program\Sponsor;
use SharedContext\Domain\ValueObject\ProgramType;

class Program
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
     * @var string||null
     */
    protected $description = null;

    /**
     * 
     * @var FirmFileInfo|null
     */
    protected $illustration;

    /**
     * 
     * @var int|null
     */
    protected $price;

    /**
     * 
     * @var bool
     */
    protected $autoAccept;

    /**
     *
     * @var ParticipantTypes
     */
    protected $participantTypes;

    /**
     * 
     * @var ProgramType
     */
    protected $programType;

    /**
     *
     * @var bool
     */
    protected $published = false;

    /**
     * 
     * @var bool
     */
    protected $strictMissionOrder;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    /**
     * 
     * @var ArrayCollection
     */
    protected $profileForms;

    /**
     * 
     * @var ArrayCollection
     */
    protected $sponsors;

    /**
     * 
     * @var ArrayCollection
     */
    protected $missions;

    function getFirm(): Firm
    {
        return $this->firm;
    }

    function getId(): string
    {
        return $this->id;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getDescription(): ?string
    {
        return $this->description;
    }

    public function getIllustration(): ?FirmFileInfo
    {
        return $this->illustration;
    }

    function isPublished(): bool
    {
        return $this->published;
    }

    public function isStrictMissionOrder(): bool
    {
        return $this->strictMissionOrder;
    }

    function isRemoved(): bool
    {
        return $this->removed;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function isAutoAccept(): bool
    {
        return $this->autoAccept;
    }

    public function getActiveMissionCount(): int
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('published', true));
        return $this->missions->matching($criteria)->count();
    }

    protected function __construct()
    {
        
    }

    public function firmEquals(Firm $firm): bool
    {
        return $firm === $this->firm;
    }

    public function getParticipantTypeValues(): array
    {
        return $this->participantTypes->getValues();
    }

    public function getProgramTypeValue(): string
    {
        return $this->programType->getDisplayValue();
    }

    public function hasProfileForm(): bool
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('disabled', false));
        return !empty($this->profileForms->matching($criteria)->count());
    }

    /**
     * 
     * @return Sponsor[]
     */
    public function iterateActiveSponsort()
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq('disabled', false));
        return $this->sponsors->matching($criteria)->getIterator();
    }

}
