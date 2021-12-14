<?php

namespace Query\Domain\Service;

use Query\Domain\Model\Firm\FeedbackForm;

interface FeedbackFormRepository
{

    public function ofId(string $id): FeedbackForm;
}
