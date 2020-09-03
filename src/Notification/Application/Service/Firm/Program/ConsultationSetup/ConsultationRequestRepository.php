<?php

namespace Notification\Application\Service\Firm\Program\ConsultationSetup;

interface ConsultationRequestRepository
{

    public function aConsultationRequestOfClientParticipant(
            string $firmId, string $clientId, string $programParticipationId, string $consultationRequestId): ConsultationRequest;

    public function aConsultationRequestOfUserParticipant(
            string $userId, string $programParticipationId, string $consultationRequestId): ConsultationRequest;

    public function aConsultationRequestOfConsultant(
            string $firmId, string $personnelId, string $programConsultationId, string $consultationRequestId): ConsultationRequest;
}
