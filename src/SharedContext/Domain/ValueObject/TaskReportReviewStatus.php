<?php

namespace SharedContext\Domain\ValueObject;

use Resources\Exception\RegularException;

class TaskReportReviewStatus
{

    const UNREVIEWED = 1;
    const APPROVED = 2;
    const REVISION_REQUIRED = 3;
    const DISPLAY_VALUE = [
        self::UNREVIEWED => 'unreviewed',
        self::APPROVED => 'approved',
        self::REVISION_REQUIRED => 'revision-required',
    ];

    /**
     * 
     * @var int
     */
    protected $value;

    public function getDisplayValue(): ?string
    {
        return self::DISPLAY_VALUE[$this->value];
    }

    public function __construct()
    {
        $this->value = self::UNREVIEWED;
    }

    public function approve(): self
    {
        $status = clone $this;
        $status->value = self::APPROVED;
        return $status;
    }

    public function askForRevision(): self
    {
        $this->assertNotApproved();
        $status = clone $this;
        $status->value = self::REVISION_REQUIRED;
        return $status;
    }

    public function revise(): self
    {
        $this->assertNotApproved();
        $status = clone $this;
        $status->value = self::UNREVIEWED;
        return $status;
    }

    protected function assertNotApproved(): void
    {
        if ($this->value === self::APPROVED) {
            throw RegularException::forbidden('report already approved, unable to make further change');
        }
    }

}
