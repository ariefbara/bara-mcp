<?php

namespace Client\Application\Service\Client\ProgramParticipation;

use Client\Domain\Model\Client\ProgramParticipation\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationRequest $consultationRequest): void;

    public function update(): void;

    public function ofId(
            ProgramParticipationCompositionId $programParticipationCompositionId, string $consultationRequestId): ConsultationRequest;
}
