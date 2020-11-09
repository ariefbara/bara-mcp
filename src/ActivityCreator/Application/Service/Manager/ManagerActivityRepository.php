<?php

namespace ActivityCreator\Application\Service\Manager;

use ActivityCreator\Domain\Model\ManagerActivity;

interface ManagerActivityRepository
{

    public function nextIdentity(): string;

    public function add(ManagerActivity $managerActivity): void;

    public function aManagerActivityOfId(string $firmId, string $managerId, string $managerActivityId): ManagerActivity;

    public function update(): void;
}
