<?php

namespace Query\Domain\Model\Shared;

interface IFieldRecord
{

    public function correspondWithFieldName(string $fieldName): bool;

    public function getValue();
}
