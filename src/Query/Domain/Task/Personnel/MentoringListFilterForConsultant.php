<?php

namespace Query\Domain\Task\Personnel;

use Query\Domain\Task\Dependency\MentoringListFilter;
use SharedContext\Domain\ValueObject\DeclaredMentoringStatus;
use SharedContext\Domain\ValueObject\MentoringRequestStatus;

class MentoringListFilterForConsultant {

    const MENTORING_REQUEST_TYPE = 'mentoring-request';
    const MENTORING_SLOT_TYPE = 'mentoring-slot';
    const DECLARED_MENTORING_TYPE = 'declared-mentoring';
    //
    const CONFIRMED_STATUS = 'confirmed';
    const NEGOTIATING_STATUS = 'negotiating';

    /**
     * 
     * @var MentoringListFilter
     */
    protected $mentoringListFilter;

    /**
     * 
     * @var string|null
     */
    protected $programId;

    /**
     * 
     * @var string|null
     */
    protected $participantId;

    /**
     * 
     * @var string|null
     */
    protected $typeList;

    /**
     * 
     * @var string|null
     */
    protected $status;

    /**
     * 
     * @var bool|null
     */
    protected $reportSubmitted;

    public function setProgramId(?string $programId) {
        $this->programId = $programId;
        return $this;
    }

    public function setParticipantId(?string $participantId) {
        $this->participantId = $participantId;
        return $this;
    }

    public function addType(?string $type) {
        if (in_array($type, [
                    self::MENTORING_REQUEST_TYPE,
                    self::MENTORING_SLOT_TYPE,
                    self::DECLARED_MENTORING_TYPE
                ])) {
            $this->typeList[] = $type;
        }
        return $this;
    }

    public function setStatus(?string $status) {
        $this->status = $status;
        return $this;
    }

    public function setReportSubmitted(?bool $reportSubmitted) {
        $this->reportSubmitted = $reportSubmitted;
        return $this;
    }

    public function __construct(MentoringListFilter $mentoringListFilter) {
        $this->mentoringListFilter = $mentoringListFilter;
    }

    protected function getProgramIdCriteria(&$parameters): ?string {
        if (empty($this->programId)) {
            return null;
        }
        $parameters['programId'] = $this->programId;
        return <<<_STATEMENT
    AND Consultant.Program_id = :programId
_STATEMENT;
    }

    protected function getParticipantIdCriteria(&$parameters): ?string {
        if (empty($this->participantId)) {
            return null;
        }
        $parameters['participantId'] = $this->participantId;
        return <<<_STATEMENT
    AND _mentoring.participantId = :participantId
_STATEMENT;
    }

    protected function getTypeCriteria(): ?string {
        if (empty($this->typeList)) {
            return null;
        }
        $typeSelection = "";
        foreach (array_unique($this->typeList) as $type) {
            switch ($type) {
                case self::MENTORING_REQUEST_TYPE:
                    $typeSelection .= empty($typeSelection) ? "_mentoring.mentoringRequestId IS NOT NULL" : " OR _mentoring.mentoringRequestId IS NOT NULL";
                    break;
                case self::MENTORING_SLOT_TYPE:
                    $typeSelection .= empty($typeSelection) ? "_mentoring.mentoringSlotId IS NOT NULL" : " OR _mentoring.mentoringSlotId IS NOT NULL";
                    break;
                case self::DECLARED_MENTORING_TYPE:
                    $typeSelection .= empty($typeSelection) ? "_mentoring.declaredMentoringId IS NOT NULL" : " OR _mentoring.declaredMentoringId IS NOT NULL";
                    break;
                default:
                    break;
            }
        }
        return empty($typeSelection) ? null : <<<_STATEMENT
    AND ({$typeSelection})
_STATEMENT;
    }

    protected function getStatusCriteria(): ?string {
        if (empty($this->status)) {
            return null;
        }
        switch ($this->status) {
            case self::CONFIRMED_STATUS:
                $confirmedMentoringRequstStatus = implode(", ", [
                    MentoringRequestStatus::ACCEPTED_BY_PARTICIPANT,
                    MentoringRequestStatus::APPROVED_BY_MENTOR
                ]);
                $confirmedDeclaredMentoringStatus = implode(", ", [
                    DeclaredMentoringStatus::APPROVED_BY_MENTOR,
                    DeclaredMentoringStatus::APPROVED_BY_PARTICIPANT,
                ]);
                return <<<_STATEMENT
    AND (
        _mentoring.mentoringRequestStatus IN ({$confirmedMentoringRequstStatus})
        OR _mentoring.declaredMentoringStatus IN ({$confirmedDeclaredMentoringStatus})
        OR _mentoring.totalBooking IS NOT NULL
    )
_STATEMENT;
            case self::NEGOTIATING_STATUS:
                $negotiatingMentoringRequstStatus = implode(", ", [
                    MentoringRequestStatus::REQUESTED,
                    MentoringRequestStatus::OFFERED
                ]);
                $negotiatingDeclaredMentoringStatus = implode(", ", [
                    DeclaredMentoringStatus::DECLARED_BY_MENTOR,
                    DeclaredMentoringStatus::DECLARED_BY_PARTICIPANT,
                ]);
                return <<<_STATEMENT
    AND (
        _mentoring.mentoringRequestStatus IN ({$negotiatingMentoringRequstStatus})
        OR _mentoring.declaredMentoringStatus IN ({$negotiatingDeclaredMentoringStatus})
        OR (COALESCE(_mentoring.totalBooking, 0) < _mentoring.capacity AND _mentoring.capacity IS NOT NULL)
    )
_STATEMENT;
            default:
                return null;
        }
    }

    protected function getReportSubmittedCriteria(): ?string {
        if (is_null($this->reportSubmitted)) {
            return null;
        }
        if ($this->reportSubmitted) {
            return <<<_STATEMENT
    AND (
        _mentoring.reportSubmitted = true 
        OR (_mentoring.totalSubmittedReport = _mentoring.totalBooking AND _mentoring.totalBooking IS NOT NULL)
    )
_STATEMENT;
        } else {
            return <<<_STATEMENT
    AND (
        (_mentoring.reportSubmitted = false OR _mentoring.reportSubmitted IS NULL) 
        AND (
            _mentoring.totalSubmittedReport < _mentoring.totalBooking 
            OR (_mentoring.totalSubmittedReport IS NULL)
        )
    )
_STATEMENT;
        }
    }

    public function getCriteriaStatement(&$parameters): ?string {
        return $this->mentoringListFilter->getCriteriaStatement($parameters)
                . $this->getProgramIdCriteria($parameters)
                . $this->getParticipantIdCriteria($parameters)
                . $this->getTypeCriteria()
                . $this->getStatusCriteria()
                . $this->getReportSubmittedCriteria();
    }

    public function getOrderStatement(): ?string {
        return $this->mentoringListFilter->getOrderStatement();
    }

    public function getLimitStatement(): ?string {
        return $this->mentoringListFilter->getLimitStatement();
    }

}
