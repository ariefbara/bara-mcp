<?php

namespace Firm\Domain\Service;

use Firm\Domain\Model\Firm\FeedbackForm;

interface FeedbackFormRepository
{

    public function aFeedbackFormOfId(string $feedbackFormId): FeedbackForm;
}
