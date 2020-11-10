<?php

namespace Query\Application\Auth\Firm;

interface PersonnelRepository
{
    public function containRecordOfActivePersonnelInFirm(string $firmId, string $personnelId): bool;
}
