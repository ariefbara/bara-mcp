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

    public function __construct(
            ?string $name, ?string $description, ?bool $strictMissionOrder, ?string $firmFileInfoIdOfIllustration)
    {
        $this->name = $name;
        $this->description = $description;
        $this->strictMissionOrder = $strictMissionOrder;
        $this->participantTypes = [];
        $this->firmFileInfoIdOfIllustration = $firmFileInfoIdOfIllustration;
    }
    
    public function addParticipantType(string $type): void
    {
        $this->participantTypes[] = $type;
    }

}
