<?php

namespace Firm\Application\Service\Manager;

use Firm\Domain\Model\Firm\FeedbackForm;

interface FeedbackFormRepository
{
    public function aFeedbackFormOfId(string $feedbackFormId): FeedbackForm;
}
