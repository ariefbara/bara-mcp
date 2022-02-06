<?php

namespace Firm\Domain\Model\Firm\Program\Mission;

use Firm\Domain\Model\Firm\FirmFileInfo;
use SplObjectStorage;

class LearningMaterialData
{

    /**
     * 
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var string|null
     */
    protected $content;

    /**
     * 
     * @var SplObjectStorage
     */
    protected $firmFileInfoAttachmentList;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function __construct(?string $name, ?string $content)
    {
        $this->name = $name;
        $this->content = $content;
        $this->firmFileInfoAttachmentList = new \SplObjectStorage();
    }

    public function addAttachment(FirmFileInfo $firmFileInfo): void
    {
        $this->firmFileInfoAttachmentList->attach($firmFileInfo);
    }

    /**
     * if firmFileInfo in list (attachment still used) remove from list and return true
     * otherwise (attachment no longer used) return false
     * @param FirmFileInfo $firmFileInfo
     * @return bool
     */
    public function removeFirmFileInfoFromList(FirmFileInfo $firmFileInfo): bool
    {
        if ($this->firmFileInfoAttachmentList->contains($firmFileInfo)) {
            $this->firmFileInfoAttachmentList->detach($firmFileInfo);
            return true;
        }
        return false;
    }

    /**
     * 
     * @return FirmFileInfo[]
     */
    public function iterateFirmFileInfoInAttachmentList(): iterable
    {
        return $this->firmFileInfoAttachmentList;
    }

}
