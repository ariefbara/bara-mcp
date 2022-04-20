<?php

namespace ExternalResource\Domain\Model;

interface ExternalTask
{
    public function execute($payload): void;
}
