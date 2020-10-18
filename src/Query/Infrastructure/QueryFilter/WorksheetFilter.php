<?php

namespace Query\Infrastructure\QueryFilter;

class WorksheetFilter
{

    /**
     *
     * @var string|null
     */
    protected $missionId;

    /**
     *
     * @var string|null
     */
    protected $parentId;

    /**
     *
     * @var bool|null
     */
    protected $hasParent;

    public function getMissionId(): ?string
    {
        return $this->missionId;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function isHasParent(): ?bool
    {
        return $this->hasParent;
    }

    public function __construct()
    {
        
    }

    public function setMissionId(?string $missionId)
    {
        $this->missionId = $missionId;
        return $this;
    }

    public function setParentId(?string $parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }

    public function setHasParent(?bool $hasParent)
    {
        $this->hasParent = $hasParent;
        return $this;
    }

}
