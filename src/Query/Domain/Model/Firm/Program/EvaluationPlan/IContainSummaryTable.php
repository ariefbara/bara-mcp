<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan;

use Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable\HeaderColumn;

interface IContainSummaryTable
{

    public function addHeaderColumn(HeaderColumn $headerColumn): void;
}
