<?php

namespace Resources\Domain\ValueObject;

class TimeUnitInterval
{
    /**
     *
     * @var string
     */
    protected $unit;

    /**
     *
     * @var int|null
     */
    protected $startYear;

    /**
     *
     * @var int|null
     */
    protected $startMonth;

    /**
     *
     * @var int|null
     */
    protected $startDate;

    /**
     *
     * @var int|null
     */
    protected $startWeek;

    /**
     *
     * @var int|null
     */
    protected $endYear;

    /**
     *
     * @var int|null
     */
    protected $endMonth;

    /**
     *
     * @var int|null
     */
    protected $endDate;

    /**
     *
     * @var int|null
     */
    protected $endWeek;
}
