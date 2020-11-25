<?php

namespace User\Application\Service\Personnel;

use User\Domain\Model\Personnel;

interface PersonnelRepository
{
    public function aPersonnelInFirmByEmailAndIdentifier(string $firmIdentifier, string $email): Personnel;
    
    public function update(): void;
}
