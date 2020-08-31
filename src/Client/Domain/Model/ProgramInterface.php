<?php

namespace Client\Domain\Model;

interface ProgramInterface
{

    public function firmIdEquals(string $firmId): bool;

    public function isRegistrationOpenFor(string $participantType): bool;

    public function getId(): string;
}
