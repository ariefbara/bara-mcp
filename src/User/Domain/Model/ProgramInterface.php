<?php

namespace User\Domain\Model;

interface ProgramInterface
{

    public function getId(): string;

    public function isRegistrationOpenFor(string $participantType): bool;
}
