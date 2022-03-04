<?php

namespace Firm\Domain\Model\Firm;

class ProgramData
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
    protected $description;

    /**
     * 
     * @var bool|null
     */
    protected $strictMissionOrder;

    /**
     *
     * @var array|null
     */
    protected $participantTypes = [];

    /**
     * 
     * @var FirmFileInfo|null
     */
    protected $illustration;

    /**
     * 
     * @var string|null
     */
    protected $programType;

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

    public function getIllustration(): ?FirmFileInfo
    {
        return $this->illustration;
    }
    
    public function getProgramType(): ?string
    {
        return $this->programType;
    }

    public function __construct(
            ?string $name, ?string $description, ?bool $strictMissionOrder, ?FirmFileInfo $illustration,
            ?string $programType)
    {
        $this->name = $name;
        $this->description = $description;
        $this->strictMissionOrder = $strictMissionOrder;
        $this->illustration = $illustration;
        $this->programType = $programType;
    }

    public function addParticipantType(string $type): void
    {
        $this->participantTypes[] = $type;
    }

}
