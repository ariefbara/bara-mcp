<?php

namespace Firm\Domain\Task\InFirm;

class LearningMaterialRequest
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
     * @var array
     */
    protected $attachedFirmFileInfoIdList;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getAttachedFirmFileInfoIdList(): array
    {
        return $this->attachedFirmFileInfoIdList;
    }

    public function __construct(?string $name, ?string $content)
    {
        $this->name = $name;
        $this->content = $content;
        $this->attachedFirmFileInfoIdList = [];
    }

    public function attachFirmFileInfoId(string $firmFileInfoId): void
    {
        $this->attachedFirmFileInfoIdList[] = $firmFileInfoId;
    }

}
