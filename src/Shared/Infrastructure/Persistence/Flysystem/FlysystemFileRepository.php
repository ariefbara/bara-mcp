<?php

namespace Shared\Infrastructure\Persistence\Flysystem;

use League\Flysystem\Filesystem;
use Shared\Domain\Service\FileRepository;

class FlysystemFileRepository implements FileRepository
{
    protected $filessystem;
    
    function __construct(Filesystem $filessystem)
    {
        $this->filessystem = $filessystem;
    }

    public function delete(string $fullyQualifiedFileName): bool
    {
        return $this->filessystem->delete($fullyQualifiedFileName);
    }

    public function has(string $fullyQualifiedFileName): bool
    {
        return $this->filessystem->has($fullyQualifiedFileName);
    }

    public function update(string $fullyQualifiedFileName, $contents): bool
    {
        return $this->filessystem->update($fullyQualifiedFileName, $contents);
    }

    public function write(string $fullyQualifiedFileName, $contents): bool
    {
        return $this->filessystem->write($fullyQualifiedFileName, $contents);
    }

}
