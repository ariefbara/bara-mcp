<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\ViewLearningMaterialActivityLog;

interface ViewLearningMaterialActivityLogRepository
{

    public function nextIdentity(): string;

    public function add(ViewLearningMaterialActivityLog $viewLearningMaterialActivityLog): void;
}
