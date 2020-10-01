<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationRequest $consultationRequest): void;

    public function aConsultationRequestFromClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest;

    public function aConsultationRequestFromUserParticipant(
            $userId, string $programParticipationId, string $consultationRequestId): ConsultationRequest;
    
    public function ofId(string $consultationRequestId): ConsultationRequest;

    public function update(): void;
}
