<?php

namespace User\Domain\Model;

use Config\EventList;
use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Event\CommonEvent,
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
    protected $active;
    
    protected function __construct()
    {
        
    }
    
    public function generateResetPasswordCode(): void
    {
        $this->assertActive();
        
        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy("+24 hours");
        
        $event = new CommonEvent(EventList::PERSONNEL_RESET_PASSWORD_CODE_GENERATED, $this->id);
        $this->recordEvent($event);
    }
    
    public function resetPassword(string $resetPasswordCode, string $password): void
    {
        $this->assertActive();
        if (empty($this->resetPasswordCode)
                || $this->resetPasswordCode !== $resetPasswordCode
                || $this->resetPasswordCodeExpiredTime < new \DateTimeImmutable()
        ) {
            $this->resetPasswordCode = null;
            $this->resetPasswordCodeExpiredTime = null;
            
            $errorDetail = "forbidden: invalid or expired token";
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($password);
        $this->resetPasswordCode = null;
        $this->resetPasswordCodeExpiredTime = null;
    }
    
    protected function assertActive(): void
    {
        if (!$this->active) {
            $errorDetail = "forbiden: only active personnel can make this request";
            throw RegularException::forbidden($errorDetail);
        }
    }
    
}
