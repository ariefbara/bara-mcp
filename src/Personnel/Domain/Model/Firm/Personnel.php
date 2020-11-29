<?php

namespace Personnel\Domain\Model\Firm;

use DateTimeImmutable;
use Resources\ {
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName,
    Exception\RegularException,
    ValidationRule,
    ValidationService
};

class Personnel
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
     * @var string|nulll
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

    public function getFirmId(): string
    {
        return $this->firmId;
    }

    function getId(): string
    {
        return $this->id;
    }

    protected function setPhone(?string $phone): void
    {
        $errorDetail = "bad request: personnel phone format is invalid";
        ValidationService::build()
                ->addRule(ValidationRule::optional(ValidationRule::phone()))
                ->execute($phone, $errorDetail);
        $this->phone = $phone;
        ;
    }

    protected function __construct()
    {
        
    }

    public function updateProfile(PersonnelProfileData $data): void
    {
        $this->assertActive();
        $this->name = new PersonName($data->getFirstName(), $data->getLastName());
        $this->setPhone($data->getPhone());
        $this->bio = $data->getBio();
    }

    public function changePassword(string $previousPassword, string $newPassword): void
    {
        $this->assertActive();
        if (!$this->password->match($previousPassword)) {
            $errorDetail = "forbidden: previous password not match";
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($newPassword);
    }
    
    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbidden: only active personnel can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }

}
