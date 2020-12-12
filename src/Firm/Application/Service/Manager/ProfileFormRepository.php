<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\ProfileForm;

interface ProfileFormRepository
{

    public function nextIdentity(): string;

    public function add(ProfileForm $profileForm): void;

    public function ofId(string $profileFormId): ProfileForm;

    public function update(): void;
}
