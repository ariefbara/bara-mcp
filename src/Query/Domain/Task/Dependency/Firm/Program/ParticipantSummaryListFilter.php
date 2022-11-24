<?php

namespace Query\Domain\Task\Dependency\Firm\Program;

use Resources\PaginationFilter;

class ParticipantSummaryListFilter
{

    const ORDER_BY_MISSION_COMPLETION_ASC = 'mission-completion-asc';
    const ORDER_BY_MISSION_COMPLETION_DESC = 'mission-completion-desc';
    const ORDER_BY_METRIC_ACHIEVEMENT_ASC = 'metric-achievement-asc';
    const ORDER_BY_METRIC_ACHIEVEMENT_DESC = 'metric-achievement-desc';

    /**
     * 
     * @var PaginationFilter
     */
    protected $paginationFilter;

    /**
     * 
     * @var string|null
     */
    protected $name;

    /**
     * 
     * @var int|null
     */
    protected $missionCompletionFrom;

    /**
     * 
     * @var int|null
     */
    protected $missionCompletionTo;

    /**
     * 
     * @var int|null
     */
    protected $metricAchievementFrom;

    /**
     * 
     * @var int|null
     */
    protected $metricAchievementTo;

    /**
     * 
     * @var string
     */
    protected $order;

    public function setName(?string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function setMissionCompletionFrom(?int $missionCompletionFrom)
    {
        $this->missionCompletionFrom = $missionCompletionFrom;
        return $this;
    }

    public function setMissionCompletionTo(?int $missionCompletionTo)
    {
        $this->missionCompletionTo = $missionCompletionTo;
        return $this;
    }

    public function setMetricAchievementFrom(?int $metricAchievementFrom)
    {
        $this->metricAchievementFrom = $metricAchievementFrom;
        return $this;
    }

    public function setMetricAchievementTo(?int $metricAchievementTo)
    {
        $this->metricAchievementTo = $metricAchievementTo;
        return $this;
    }

    public function setOrder(string $order)
    {
        $this->order = $order;
        return $this;
    }

    public function __construct(PaginationFilter $paginationFilter)
    {
        $this->paginationFilter = $paginationFilter;
    }
    //
    protected function getNameCriteria(&$parameters): ?string
    {
        if (empty($this->name)) {
            return null;
        }
        $parameters['name'] = "%{$this->name}%";
        return <<<_STATEMENT
    AND participantName LIKE = :name
_STATEMENT;
    }
    protected function getMissionCompletionFromCriteria(&$parameters): ?string
    {
        if (empty($this->missionCompletionFrom)) {
            return null;
        }
        $parameters['missionCompletionFrom'] = $this->missionCompletionFrom;
        return <<<_STATEMENT
    AND participantName LIKE = :name
_STATEMENT;
    }
    protected function getMissionCompletionToCriteria(&$parameters): ?string
    {
        
    }
    protected function getMetricAchievementFromCriteria(&$parameters): ?string
    {
        
    }
    protected function getMetricAchievementToCriteria(&$parameters): ?string
    {
        
    }
    
    //
    public function getCriteriaStatement(&$parameters): ?string
    {
        return $this->getNameCriteria($parameters)
                . $this->getMissionCompletionFromCriteria($parameters)
                . $this->getMissionCompletionToCriteria($parameters)
                . $this->getMetricAchievementFromCriteria($parameters)
                . $this->getMetricAchievementToCriteria($parameters);
    }

    public function getOrderStatement(): ?string
    {
        
    }

    public function getLimitStatement(): ?string
    {
        return $this->paginationFilter->getLimitStatement();
    }

}
