<?php

namespace Resources;

interface SearchCriteriaInterface
{

    public function getEvaluationStatement(&$parameters): ?string;

    public function getTableColumnName(): string;
}
