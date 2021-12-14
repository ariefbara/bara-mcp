<?php

namespace Query\Domain\Task\Dependency\Firm;

use Query\Domain\Model\Firm\FeedbackForm;

interface FeedbackFormRepository
{

    public function aFeedbackFormOfId(string $id): FeedbackForm;
}
