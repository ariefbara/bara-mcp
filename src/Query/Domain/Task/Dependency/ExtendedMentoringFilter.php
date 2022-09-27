<?php

namespace Query\Domain\Task\Dependency;

use DateTimeImmutable;
use ReflectionClass;
use Resources\Exception\RegularException;

class ExtendedMentoringFilter
{

    const ASC = 'ASC';
    const DESC = 'DESC';

    /**
     * 
     * @var int
     */
    protected $page;

    /**
     * 
     * @var int
     */
    protected $pageSize;

    /**
     * 
     * @var string|null
     */
    protected $participantId;

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

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getParticipantId(): ?string
    {
        return $this->participantId;
    }

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

    public function __construct(int $page, int $pageSize)
    {
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public function setParticipantId(?string $participantId)
    {
        $this->participantId = $participantId;
        return $this;
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
        return "AND $tableName.endTime >= :from";
    }

    public function getSqlToClause(string $tableName, array &$parameters): ?string
    {
        if (empty($this->to)) {
            return null;
        }
        $parameters['to'] = $this->to->format('Y-m-d H:i:s');
        return "AND $tableName.endTime < :to";
    }

    public function getSqlParticipantIdClause(string $tableName, array &$parameters): ?string
    {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return "AND $tableName.id = :participantId";
    }

}
