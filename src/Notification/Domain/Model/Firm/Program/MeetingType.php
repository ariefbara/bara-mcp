<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Program;

class MeetingType
{

    /**
     *
     * @var Program
     */
    protected $program;

    /**
     *
     * @var string
     */
    protected $id;

    protected function __construct()
    {
        
    }

    public function getFirmDomain(): string
    {
        return $this->program->getFirmDomain();
    }

    public function getFirmLogoPath(): ?string
    {
        return $this->program->getFirmLogoPath();
    }

    public function getFirmMailSenderAddress(): string
    {
        return $this->program->getFirmMailSenderAddress();
    }

    public function getFirmMailSenderName(): string
    {
        return $this->program->getFirmMailSenderName();
    }

}
