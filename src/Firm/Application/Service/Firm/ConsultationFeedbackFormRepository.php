<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\ConsultationFeedbackForm;

interface ConsultationFeedbackFormRepository
{

    public function nextIdentity(): string;

    public function add(ConsultationFeedbackForm $consultationFeedbackForm): void;

    public function update(): void;

    public function ofId(string $firmId, string $consultationFeedbackFormId): ConsultationFeedbackForm;

    public function all(string $firmId, int $page, int $pageSize);
}
