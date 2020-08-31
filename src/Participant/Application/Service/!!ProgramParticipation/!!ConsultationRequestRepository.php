<?php

namespace User\Application\Service\User\ProgramParticipation;

use User\Domain\Model\User\ProgramParticipation\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationRequest $consultationRequest): void;

    public function update(): void;

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): ConsultationRequest;
}
