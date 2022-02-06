<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Mission;

use Query\Domain\Task\PaginationPayload;

class LearningMaterialFilter
{

    /**
     * 
     * @var PaginationPayload
     */
    protected $pagination;

    /**
     * 
     * @var string|null
     */
    protected $missionId;

    public function getPagination(): PaginationPayload
    {
        return $this->pagination;
    }

    public function getMissionId(): ?string
    {
        return $this->missionId;
    }

    public function __construct(PaginationPayload $pagination)
    {
        $this->pagination = $pagination;
    }

    public function setMissionId(?string $missionId): self
    {
        $this->missionId = $missionId;
        return $this;
    }

}
