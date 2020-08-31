<?php

namespace Participant\Application\Service\Participant;

use Participant\Domain\Model\Participant\ConsultationRequest;

interface ConsultationRequestRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationRequest $consultationRequest): void;

    public function consultationRequestFromClient(string $firmId, string $clientId, string $programId, string $consultationRequestId): ConsultationRequest;
    
    public function consultationRequestFromUser(string $userId, string $firmId, string $programId, string $consultationRequestId): ConsultationRequest;

    public function update(): void;
}
