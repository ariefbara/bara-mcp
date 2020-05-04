<?php

namespace Bara\Domain\Model;

use Resources\{
    Domain\ValueObject\Password,
    Exception\RegularException,
    ValidationRule,
    ValidationService
};

class Admin
{

    protected $id;

    /**
     * 
     * @var string
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
     * @var bool
     */
    protected $removed = false;

    protected function setName($name): void
    {
        $errorDetail = 'bad request: sys admin name is required';
        ValidationService::build()
                ->addRule(ValidationRule::notEmpty())
                ->execute($name, $errorDetail);
        $this->name = $name;
    }

    protected function setEmail($email): void
    {
        $errorDetail = 'bad request: sys admin email is required and must be in valid email format';
        ValidationService::build()
                ->addRule(ValidationRule::email())
                ->execute($email, $errorDetail);
        $this->email = $email;
    }

    function __construct(string $id, AdminData $adminData, string $password)
    {
        $this->id = $id;
        $this->setName($adminData->getName());
        $this->setEmail($adminData->getEmail());
        $this->password = new Password($password);
        $this->removed = false;
    }

    public function updateProfile(AdminData $adminData): void
    {
        $this->setName($adminData->getName());
        $this->setEmail($adminData->getEmail());
    }

    public function changePassword(string $previousPassword, string $newPassword): void
    {
        if (!$this->password->match($previousPassword)) {
            $errorDetail = 'forbidden: previous password not match';
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($newPassword);
    }

    public function emailEquals(string $email): bool
    {
        return $this->email == $email;
    }

    public function remove(): void
    {
        $this->removed = true;
    }

}
