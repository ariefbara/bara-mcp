<?php

namespace Query\Domain\Task\Dependency;

use DateTimeImmutable;
use Resources\PaginationFilter;

class NoteFilter
{

    const MODIFIED_TIME_ASC_ORDER = 'modified-asc';
    const MODIFIED_TIME_DESC_ORDER = 'modified-desc';
    const CREATED_TIME_ASC_ORDER = 'created-desc';
    const CREATED_TIME_DESC_ORDER = 'created-desc';
    
    const CONSULTANT_NOTE = 'consultant';
    const COORDINATOR_NOTE = 'coordinator';
    const PARTICIPANT_NOTE = 'participant';

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

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
    protected $keyword;

    /**
     * 
     * @var string|null
     */
    protected $source;

    /**
     * 
     * @var string|null
     */
    protected $order;

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

    public function setKeyword(?string $keyword)
    {
        $this->keyword = $keyword;
        return $this;
    }

    public function setSource(?string $source)
    {
        $this->source = $source;
        return $this;
    }

    public function setOrder(?string $order)
    {
        $this->order = $order;
        return $this;
    }

    public function __construct(?PaginationFilter $paginationFilter = null)
    {
        $this->paginationFilter = $paginationFilter;
    }
    
    protected function getFromOptionalStatement(&$parameters): ?string
    {
        if (empty($this->from)) {
            return null;
        }
        $parameters['from'] = $this->from->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Note.modifiedTime >= :from
_STATEMENT;
    }

    protected function getToOptionalStatement(&$parameters): ?string
    {
        if (empty($this->to)) {
            return null;
        }
        $parameters['to'] = $this->to->format('Y-m-d H:i:s');
        return <<<_STATEMENT
    AND Note.modifiedTime <= :to
_STATEMENT;
    }

    protected function getKeywordOptionalStatement(&$parameters): ?string
    {
        if (empty($this->keyword)) {
            return null;
        }
        $parameters['keyword'] = "%{$this->keyword}%";
        return <<<_STATEMENT
    AND (Note.name LIKE :keyword OR Note.description LIKE :keyword)
_STATEMENT;
    }

    protected function getNoteSourceOptionalStatement(): ?string
    {
        switch ($this->source) {
            case self::CONSULTANT_NOTE:
                return 'AND ConsultantNote.id IS NOT NULL';
                break;
            case self::COORDINATOR_NOTE:
                return 'AND CoordinatorNote.id IS NOT NULL';
                break;
            case self::PARTICIPANT_NOTE:
                return 'AND ParticipantNote.id IS NOT NULL';
                break;
            default:
                return null;
                break;
        }
    }
    
    public function getOptionalConditionStatement(&$parameters): ?string
    {
        return $this->getFromOptionalStatement($parameters)
                . $this->getToOptionalStatement($parameters)
                . $this->getKeywordOptionalStatement($parameters)
                . $this->getNoteSourceOptionalStatement();
    }
    
    public function getOrderStatement(): ?string
    {
        switch ($this->order) {
            case self::MODIFIED_TIME_ASC_ORDER:
                return 'ORDER BY modifiedTime ASC';
                break;
            case self::CREATED_TIME_ASC_ORDER:
                return 'ORDER BY createdTime ASC';
                break;
            case self::CREATED_TIME_DESC_ORDER:
                return 'ORDER BY createdTime DESC';
                break;
            default:
                return 'ORDER BY modifiedTime DESC';
                break;
        }
    }
    
    public function getLimitStatement(): ?string
    {
        return $this->paginationFilter->getLimitStatement();
    }

}
