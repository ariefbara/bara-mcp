<?php

namespace Shared\Domain\Service;

interface FileRepository
{

    public function write(string $fullyQualifiedFileName, $contents): bool;

    public function update(string $fullyQualifiedFileName, $contents): bool;

    public function delete(string $fullyQualifiedFileName): bool;

    public function has(string $fullyQualifiedFileName): bool;
}
