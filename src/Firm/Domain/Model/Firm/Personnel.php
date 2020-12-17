<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Doctrine\Common\Collections\{
    ArrayCollection,
    Criteria
};
use Firm\Domain\Model\{
    AssetBelongsToFirm,
    Firm
};
use Resources\{
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName,
    Exception\RegularException,
    ValidationRule,
    ValidationService
};

class Personnel implements AssetBelongsToFirm
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
     * @var string
     */
    protected $phone;

    /**
     *
     * @var string
     */
    protected $bio;

    /**
     *
     * @var DateTimeImmutable
     */
    protected $joinTime;

    /**
     *
     * @var bool
     */
    protected $active;

    /**
     *
     * @var ArrayCollection
     */
    protected $programCoordinatorships;

    /**
     *
     * @var ArrayCollection
     */
    protected $programMentorships;

    function isActive(): bool
    {
        return $this->active;
    }

    protected function setEmail(string $email): void
    {
        $errorDetail = "bad request: personnel email is required in valid format";
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, $errorDetail);
        $this->email = $email;
    }

    protected function setPhone(?string $phone): void
    {
        $errorDetail = "bad request: personnel phone format is invalid";
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::phone()))
                ->execute($phone, $errorDetail);
        $this->phone = $phone;
    }

    function __construct(Firm $firm, string $id, PersonnelData $personnelData)
    {
        $this->firm = $firm;
        $this->id = $id;
        $this->name = new PersonName($personnelData->getFirstName(), $personnelData->getLastName());
        $this->setEmail($personnelData->getEmail());
        $this->password = new Password($personnelData->getPassword());
        $this->setPhone($personnelData->getPhone());
        $this->bio = $personnelData->getBio();
        $this->joinTime = new DateTimeImmutable();
        $this->active = true;
        $this->assignedAdmin = null;
    }

    public function getName(): string
    {
        return $this->name->getFullName();
    }

    public function disable(): void
    {
        $criteria = Criteria::create()
                ->andWhere(Criteria::expr()->eq("active", true));

        if (!empty($this->programCoordinatorships->matching($criteria)->count()) || !empty($this->programMentorships->matching($criteria)->count())
        ) {
            $errorDetail = "forbidden: unable to disable personnel still having active role as coordinator or mentor in program";
            throw RegularException::forbidden($errorDetail);
        }

        $this->active = false;
    }
    
    public function enable(): void
    {
        if ($this->active) {
            $errorDetail = "forbidden: personnel already active";
            throw RegularException::forbidden($errorDetail);
        }
        $this->active = true;
    }

    public function belongsToFirm(Firm $firm): bool
    {
        return $this->firm === $firm;
    }

}
