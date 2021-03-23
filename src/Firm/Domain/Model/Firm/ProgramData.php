<?php

namespace Firm\Domain\Model\Firm;

class ProgramData
{

    /**
     *
     * @var string||null
     */
    protected $name;

    /**
     *
     * @var string||null
     */
    protected $description;

    /**
     * 
     * @var bool|null
     */
    protected $strictMissionOrder;

    /**
     *
     * @var array||null
     */
    protected $participantTypes = [];

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getParticipantTypes(): ?array
    {
        return $this->participantTypes;
    }

    public function isStrictMissionOrder(): ?bool
    {
        return $this->strictMissionOrder;
    }

    public function __construct(?string $name, ?string $description, ?bool $strictMissionOrder)
    {
        $this->name = $name;
        $this->description = $description;
        $this->strictMissionOrder = $strictMissionOrder;
        $this->participantTypes = [];
    }

    public function addParticipantType(string $type): void
    {
        $this->participantTypes[] = $type;
    }

}
