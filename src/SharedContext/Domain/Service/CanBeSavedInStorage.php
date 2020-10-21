<?php

namespace SharedContext\Domain\Service;

interface CanBeSavedInStorage
{
    public function getFullyQualifiedFileName(): string;
}
