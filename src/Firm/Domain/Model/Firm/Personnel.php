<?php

namespace Firm\Domain\Model\Firm;

use DateTimeImmutable;
use Firm\Domain\Model\Firm;
use Resources\ {
    Domain\Model\Mail\Recipient,
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName,
    ValidationRule,
    ValidationService
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
    protected $removed;

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
        $this->removed = false;
        $this->assignedAdmin = null;
    }
    
    public function getMailRecipient(): Recipient
    {
        return new Recipient($this->email, $this->name);
    }
    
    public function getName(): string
    {
        return $this->name->getFullName();
    }

}
