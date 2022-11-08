<?php

namespace Query\Domain\Task\Dependency\Firm\Program\Participant;

use Query\Domain\Task\Dependency\PaginationFilter;

class ParticipantProfileFilter
{

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var string|null
     */
    protected $participantId;

    public function getPaginationFilter(): PaginationFilter
    {
        return $this->paginationFilter;
    }

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }

    public function setParticipantId(?string $participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }

}
