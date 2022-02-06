<?php

namespace Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

use Query\Domain\Model\Firm\FirmFileInfo;
use Query\Domain\Model\Firm\Program\Mission\LearningMaterial;

class LearningAttachment
{

    /**
     * 
     * @var LearningMaterial
     */
    protected $learningMaterial;

    /**
     * 
     * @var string
     */
    protected $id;

    /**
     * 
     * @var bool
     */
    protected $disabled;

    /**
     * 
     * @var FirmFileInfo
     */
    protected $firmFileInfo;

    public function getLearningMaterial(): LearningMaterial
    {
        return $this->learningMaterial;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getFirmFileInfo(): FirmFileInfo
    {
        return $this->firmFileInfo;
    }

    protected function __construct()
    {
        
    }

}
