<?php

namespace SharedContext\Domain\ValueObject;

use Resources\ {
    ValidationRule,
    ValidationService
};

class EvaluationResult
{

    const VALID_STATUS = [
        "fail", "pass", "extend",
    ];

    /**
     *
     * @var string
     */
    protected $status;

    /**
     *
     * @var int|null
     */
    protected $extendDays;

    function getStatus(): string
    {
        return $this->status;
    }

    function getExtendDays(): ?int
    {
        return $this->extendDays;
    }

    function __construct(string $status, ?int $extendDays)
    {
        $errorDetail = "bad request: invalid evaluation status";
        ValidationService::build()
                ->addRule(ValidationRule::in(self::VALID_STATUS))
                ->execute($status, $errorDetail);
        
        $this->status = $status;
        if ($this->status === "extend" ) {
            $this->extendDays = $extendDays;
        }
    }

    public function isFail(): bool
    {
        return $this->status === "fail";
    }

}
