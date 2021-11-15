<?php

namespace Query\Domain\Task\Dependency;

use DateTimeImmutable;
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
     * @var MentoringSlotFilter|null
     */
    protected $mentoringSlotFilter;

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function getOrderDirection(): ?string
    {
        return $this->orderDirection;
    }

    public function getMentoringSlotFilter(): ?MentoringSlotFilter
    {
        return $this->mentoringSlotFilter;
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

    public function setMentoringSlotFilter(?MentoringSlotFilter $mentoringSlotFilter)
    {
        $this->mentoringSlotFilter = $mentoringSlotFilter;
        return $this;
    }

    public function __construct()
    {
        
    }

}
