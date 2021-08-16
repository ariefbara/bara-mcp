<?php

namespace Query\Domain\Model\Shared;

interface IField
{
    public function getName(): string;
    
    public function extractCorrespondingValueFromRecord(FormRecord $formRecord);
}
