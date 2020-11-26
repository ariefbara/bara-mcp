<?php

namespace User\Domain\Model;

use Config\EventList;
use DateTimeImmutable;
use Resources\ {
    DateTimeImmutableBuilder,
    Domain\Event\CommonEvent,
    Domain\Model\EntityContainEvents,
    Domain\ValueObject\Password,
    Exception\RegularException
};
use SharedContext\Domain\Model\SharedEntity\FileInfoData;
use User\Domain\ {
    DependencyModel\Firm,
    Model\Manager\ManagerFileInfo
};

class Manager extends EntityContainEvents
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
    protected $removed = false;
    
    protected function __construct()
    {
        
    }
    
    public function changePassword(string $oldPassword, string $newPassword): void
    {
        if (!$this->password->match($oldPassword)) {
            $errorDetail = "forbidden: provided password doesn't match current password";
            throw RegularException::forbidden($errorDetail);
        }
        $this->password = new Password($newPassword);
    }
    
    public function generateResetPasswordCode(): void
    {
        $this->resetPasswordCode = bin2hex(random_bytes(32));
        $this->resetPasswordCodeExpiredTime = DateTimeImmutableBuilder::buildYmdHisAccuracy("+24 hours");
        
        $event = new CommonEvent(EventList::MANAGER_RESET_PASSWORD_CODE_GENERATED, $this->id);
        $this->recordEvent($event);
    }
    
    public function resetPassword(string $resetPasswordCode, string $password): void
    {
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
    
    public function saveFileInfo(string $managerFileInfoId, FileInfoData $fileInfoData): ManagerFileInfo
    {
        if ($this->removed) {
            $errorDetail = "forbidden: only active manage can make this request";
            throw RegularException::forbidden($errorDetail);
        }
        return new ManagerFileInfo($this, $managerFileInfoId, $fileInfoData);
    }
}
