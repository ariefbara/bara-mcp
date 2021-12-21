<?php

namespace Query\Infrastructure\QueryFilter;

use DateTimeImmutable;
use ReflectionClass;
use Resources\Exception\RegularException;

class ActivityFilter
{

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
     * @var bool|null
     */
    protected $cancelledStatus;

    /**
     * 
     * @var string|null
     */
    protected $order;

    /**
     * 
     * @var string[]
     */
    protected $activityTypeIdList = [];

    /**
     * 
     * @var string[]
     */
    protected $initiatorTypeList = [];

    const ASCENDING = 'ASC';
    const DESCENDING = 'DESC';
    const INITIATED_BY_MANAGER = 'manager';
    const INITIATED_BY_COORDINATOR = 'coordinator';
    const INITIATED_BY_MENTOR = 'consultant';
    const INITIATED_BY_PARTICIPANT = 'participant';

    public function getFrom(): ?DateTimeImmutable
    {
        return $this->from;
    }

    public function getTo(): ?DateTimeImmutable
    {
        return $this->to;
    }

    public function getCancelledStatus(): ?bool
    {
        return $this->cancelledStatus;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }

    public function getActivityTypeIdList(): array
    {
        return $this->activityTypeIdList;
    }

    public function getInitiatorTypeList(): array
    {
        return $this->initiatorTypeList;
    }

    public function __construct()
    {
        
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

    public function setCancelledStatus(?bool $cancelledStatus)
    {
        $this->cancelledStatus = $cancelledStatus;
        return $this;
    }

    public function setOrder(?string $order)
    {
        if (!is_null($order) && !in_array($order, [self::ASCENDING, self::DESCENDING])) {
            throw RegularException::badRequest("bad request: list order can only be 'ASC' or 'DESC'");
        }
        $this->order = $order;
        return $this;
    }

    public function addActivityTypeId(string $activityTypeId): self
    {
        $this->activityTypeIdList[] = $activityTypeId;
        return $this;
    }

    public function addInitiatorTypeList(string $initiatorType): self
    {
        $validInitiatorList = [
            self::INITIATED_BY_MANAGER,
            self::INITIATED_BY_COORDINATOR,
            self::INITIATED_BY_MENTOR,
            self::INITIATED_BY_PARTICIPANT,
        ];
        if (!is_null($initiatorType) && !in_array($initiatorType, $validInitiatorList)) {
            throw RegularException::badRequest("bad request: invalid initiator type");
        }
        $this->initiatorTypeList[] = $initiatorType;
        return $this;
    }

    public function writeFromSqlClause($tableName, array &$parameters): ?string
    {
        if (empty($this->from)) {
            return null;
        }
        $parameters['from'] = $this->from->format('Y-m-d H:i:s');
        return "AND $tableName.endDateTime >= :from";
    }

    public function writeToSqlClause($activityTableName, array &$parameters): ?string
    {
        if (empty($this->to)) {
            return null;
        }
        $parameters['to'] = $this->to->format('Y-m-d H:i:s');
        return "AND $activityTableName.endDateTime < :to";
    }

    public function writeCancelledStatusSqlClause($activityTableName, array &$parameters): ?string
    {
        if (!isset($this->cancelledStatus)) {
            return null;
        }
        $parameters['cancelledStatus'] = $this->cancelledStatus;
        return "AND $activityTableName.cancelled = :cancelledStatus";
    }

    public function writeActivityTypeSqlClause($activityTypeTableName, array &$parameters): ?string
    {
        if (empty($this->activityTypeIdList)) {
            return null;
        }
        $activityTypes = '';
        foreach ($this->activityTypeIdList as $key => $activityTypeId) {
            $activityTypes .= empty($activityTypes) ? ":activityType_$key" : ", :activityType_$key";
            $parameters["activityType_$key"] = $activityTypeId;
        }
        return "AND $activityTypeTableName.id IN ($activityTypes)";
    }

    public function writeInitiatorTypeSqlClause(
            string $managerIdColName, string $coordinatorIdColName, string $mentorIdColName,
            string $participantIdColName, array &$parameters): ?string
    {
        if (empty($this->initiatorTypeList)) {
            return null;
        }
        $initiatorTypesClause = '';
        if (in_array(self::INITIATED_BY_MANAGER, $this->initiatorTypeList)) {
            $initiatorTypesClause .= empty($initiatorTypesClause) ? "{$managerIdColName} IS NOT NULL" : " OR {$managerIdColName} IS NOT NULL";
        }
        if (in_array(self::INITIATED_BY_COORDINATOR, $this->initiatorTypeList)) {
            $initiatorTypesClause .= empty($initiatorTypesClause) ? "{$coordinatorIdColName} IS NOT NULL" : " OR {$coordinatorIdColName} IS NOT NULL";
        }
        if (in_array(self::INITIATED_BY_MENTOR, $this->initiatorTypeList)) {
            $initiatorTypesClause .= empty($initiatorTypesClause) ? "{$mentorIdColName} IS NOT NULL" : " OR {$mentorIdColName} IS NOT NULL";
        }
        if (in_array(self::INITIATED_BY_PARTICIPANT, $this->initiatorTypeList)) {
            $initiatorTypesClause .= empty($initiatorTypesClause) ? "{$participantIdColName} IS NOT NULL" : " OR {$participantIdColName} IS NOT NULL";
        }
        return "AND ($initiatorTypesClause)";
    }

    public function writeOrderSqlClause($activityTableName): ?string
    {
        return "ORDER BY $activityTableName.startDateTime " . $this->order ?: 'ASC';
    }

}
