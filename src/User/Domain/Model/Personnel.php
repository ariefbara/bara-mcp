<?php

namespace User\Domain\Model;

use DateTimeImmutable;
use Resources\ {
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\Password,
    Domain\ValueObject\PersonName,
    Exception\RegularException
};
use User\Domain\DependencyModel\Firm;

class Personnel extends EntityContainEvents
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
     * @var string|null
     */
    protected $phone;

    /**
     *
     * @var string|null
     */
    protected $bio;
    
    /**
     *
     * @var string|null
     */
    protected $resetPasswordCode;
    
    /**
     *
     * @var DateTimeImmutable|null
     */
    protected $resetPasswordCodeExpiredTime;

    /**
     *
     * @var bool
     */
    protected $removed;
    
    public function __construct()
    {
        
    }
    
    public function generateResetPasswordCode(): void
    {
        
    }
    
    public function resetPassword(string $resetPasswordCode, string $password): void
    {
        if (empty($this->resetPasswordCode)
                || $this->resetPasswordCode !== $resetPasswordCode
                || $this->resetPasswordCodeExpiredTime < new \DateTimeImmutable()
        ) {
            $errorDetail = "forbidden: invalid or expired token";
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($password);
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiredTime = null;
    }
    
}
