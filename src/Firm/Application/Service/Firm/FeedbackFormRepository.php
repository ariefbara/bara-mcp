<?php

namespace Firm\Application\Service\Firm;

use Firm\Domain\Model\Firm\FeedbackForm;

interface FeedbackFormRepository
{

    public function nextIdentity(): string;

    public function add(FeedbackForm $feedbackform): void;

    public function update(): void;

    public function ofId(string $firmId, string $feedbackFormId): FeedbackForm;
}
