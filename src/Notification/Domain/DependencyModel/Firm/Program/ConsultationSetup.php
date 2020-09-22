<?php

namespace Notification\Domain\Model\Firm\Program;

use Notification\Domain\Model\Firm\Program;
use Resources\Application\Service\SenderInterface;

class ConsultationSetup
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

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var bool
     */
    protected $removed = false;

    protected function __construct()
    {
        ;
    }
    
    public function getFirmWhitelableUrl(): string
    {
        return $this->program->getFirmWhitelableUrl();
    }
    
    public function getFirmMailSender(): SenderInterface
    {
        return $this->program->getFirmMailSender();
    }

}
