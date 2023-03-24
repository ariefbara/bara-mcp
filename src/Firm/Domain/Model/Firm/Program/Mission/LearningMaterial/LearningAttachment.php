<?php

namespace Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;

use Firm\Domain\Model\Firm\FirmFileInfo;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterial;
use Firm\Domain\Model\Firm\Program\Mission\LearningMaterialData;

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

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function __construct(LearningMaterial $learningMaterial, string $id, FirmFileInfo $firmFileInfo)
    {
        $this->learningMaterial = $learningMaterial;
        $this->id = $id;
        $this->disabled = false;
        $this->firmFileInfo = $firmFileInfo;
    }

    public function update(LearningMaterialData $learningMaterialData): void
    {
        if (!$learningMaterialData->removeFirmFileInfoFromList($this->firmFileInfo)) {
            $this->disabled = true;
        } else {
            $this->disabled = false;
        }
    }

}
