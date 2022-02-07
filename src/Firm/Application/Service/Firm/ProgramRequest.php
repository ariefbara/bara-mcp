<?php

namespace Firm\Application\Service\Firm;

class ProgramRequest
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
     * @var string|null
     */
    protected $firmFileInfoIdOfIllustration;

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

    public function getStrictMissionOrder(): ?bool
    {
        return $this->strictMissionOrder;
    }

    public function getParticipantTypes(): ?array
    {
        return $this->participantTypes;
    }

    public function getFirmFileInfoIdOfIllustration(): ?string
    {
        return $this->firmFileInfoIdOfIllustration;
    }

    public function getProgramType(): ?string
    {
        return $this->programType;
    }

    public function __construct(
            ?string $name, ?string $description, ?bool $strictMissionOrder, ?string $firmFileInfoIdOfIllustration,
            ?string $programType)
    {
        $this->name = $name;
        $this->description = $description;
        $this->strictMissionOrder = $strictMissionOrder;
        $this->firmFileInfoIdOfIllustration = $firmFileInfoIdOfIllustration;
        $this->programType = $programType;
    }

    public function addParticipantType(string $type): void
    {
        $this->participantTypes[] = $type;
    }

}
