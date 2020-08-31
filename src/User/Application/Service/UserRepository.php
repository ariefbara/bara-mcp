<?php

namespace User\Application\Service;

use User\Domain\Model\User;

interface UserRepository
{

    public function nextIdentity(): string;

    public function add(User $user): void;

    public function update(): void;

    public function ofId(string $userId): User;

    public function ofEmail(string $email): User;

    public function containRecordWithEmail(string $email): bool;
}
