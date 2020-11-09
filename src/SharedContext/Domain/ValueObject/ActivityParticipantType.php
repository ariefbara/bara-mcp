<?php

namespace SharedContext\Domain\ValueObject;

use Resources\ {
    ValidationRule,
    ValidationService
};

class ActivityParticipantType
{
    
    public const COORDINATOR = "coordinator";
    public const MANAGER = "manager";
    public const CONSULTANT = "consultant";
    public const PARTICIPANT = "participant";
    private const VALID_PARTICIPANT_TYPE = [
        "coordinator",
        "manager",
        "consultant",
        "participant",
    ];

    /**
     *
     * @var string
     */
    protected $participantType;

    function getParticipantType(): string
    {
        return $this->participantType;
    }
    
    function __construct(string $participantType)
    {
        $errorDetail = "bad request: invalid activity participant type";
        ValidationService::build()
                ->addRule(ValidationRule::in(self::VALID_PARTICIPANT_TYPE))
                ->execute($participantType, $errorDetail);
        $this->participantType = $participantType;
    }
    
    public function sameValueAs(ActivityParticipantType $other): bool
    {
        return $this->participantType === $other->participantType;
    }

}
