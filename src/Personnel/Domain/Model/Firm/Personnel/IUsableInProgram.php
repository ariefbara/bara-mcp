<?php

namespace Personnel\Domain\Model\Firm\Personnel;

interface IUsableInProgram
{
    public function assertUsableInProgram(string $programId): void;
}
