<?php

namespace Query\Domain\Model\Firm\Program\EvaluationPlan\SummaryTable;

use Query\Domain\Model\Shared\Form\IContainFieldRecord;

interface IField
{

    public function getLabel(): string;

    public function getCorrespondingValueFromRecord(IContainFieldRecord $containFieldRecord);
}
