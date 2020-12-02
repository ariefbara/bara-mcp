<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\Program\EvaluationPlan;

interface EvaluationPlanRepository
{
    public function nextIdentity(): string;
    
    public function add(EvaluationPlan $evaluationPlan): void;
    
    public function ofId(string $evaluationPlanId): EvaluationPlan;
    
    public function update(): void;
}
