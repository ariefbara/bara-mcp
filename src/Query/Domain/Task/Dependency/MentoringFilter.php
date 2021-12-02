<?php

namespace Query\Domain\Task\Dependency;

use DateTimeImmutable;
use Query\Domain\Task\Dependency\MentoringFilter\DeclaredMentoringFilter;
use Query\Domain\Task\Dependency\MentoringFilter\MentoringRequestFilter;
use Query\Domain\Task\Dependency\MentoringFilter\MentoringSlotFilter;
use ReflectionClass;
use Resources\Exception\RegularException;

class MentoringFilter
{

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $from;

    /**
     * 
     * @var DateTimeImmutable|null
     */
    protected $to;

    /**
     * 
     * @var string|null
     */
    protected $orderDirection = 'ASC';

    /**
     * 
     * @var MentoringSlotFilter
     */
    protected $mentoringSlotFilter;

    /**
     * 
     * @var MentoringRequestFilter
     */
    protected $mentoringRequestFilter;

    /**
     * 
     * @var DeclaredMentoringFilter
     */
    protected $declaredMentoringFilter;

    public function getMentoringSlotFilter(): MentoringSlotFilter
    {
        return $this->mentoringSlotFilter;
    }

    public function getMentoringRequestFilter(): MentoringRequestFilter
    {
        return $this->mentoringRequestFilter;
    }

    public function getDeclaredMentoringFilter(): DeclaredMentoringFilter
    {
        return $this->declaredMentoringFilter;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function __construct(
            MentoringSlotFilter $mentoringSlotFilter, MentoringRequestFilter $mentoringRequestFilter,
            DeclaredMentoringFilter $declaredMentoringFilter)
    {
        $this->mentoringSlotFilter = $mentoringSlotFilter;
        $this->mentoringRequestFilter = $mentoringRequestFilter;
        $this->declaredMentoringFilter = $declaredMentoringFilter;
    }

    public function setFrom(?DateTimeImmutable $from)
    {
        $this->from = $from;
        return $this;
    }

    public function setTo(?DateTimeImmutable $to)
    {
        $this->to = $to;
        return $this;
    }

    public function setOrderDirection(?string $orderDirection)
    {
        if (isset($orderDirection)) {
            $c = new ReflectionClass($this);
            if (!in_array($orderDirection, $c->getConstants())) {
                throw RegularException::badRequest('bad request: invalid order argument');
            }
            $this->orderDirection = $orderDirection;
        }
        return $this;
    }

    public function getSqlFromClause(string $tableName, array &$parameters): ?string
    {
        if (empty($this->from)) {
            return null;
        }
        $parameters['from'] = $this->from->format('Y-m-d H:i:s');
        return "AND $tableName.startTime > :from";
    }

    public function getSqlToClause(string $tableName, array &$parameters): ?string
    {
        if (empty($this->to)) {
            return null;
        }
        $parameters['to'] = $this->to->format('Y-m-d H:i:s');
        return "AND $tableName.endTime < :to";
    }

}
