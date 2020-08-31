<?php

namespace SharedContext\Domain\Model\SharedEntity;

use Resources\ {
    ValidationRule,
    ValidationService
};

class ConsultationRequestStatusVO
{

    const VALID_STATUS = [
        "proposed",
        "rejected",
        "cancelled",
        "offered",
        "scheduled",
    ];

    /**
     *
     * @var string
     */
    protected $value;

    function getValue(): string
    {
        return $this->value;
    }

    function __construct(string $value)
    {
        $errorDetail = "bad request: invalid consultation request status";
        ValidationService::build()
                ->addRule(ValidationRule::in(self::VALID_STATUS, true))
                ->execute($value, $errorDetail);
        $this->value = $value;
    }

    public function sameValueAs(ConsultationRequestStatusVO $other): bool
    {
        return $this->value === $other->value;
    }
}
