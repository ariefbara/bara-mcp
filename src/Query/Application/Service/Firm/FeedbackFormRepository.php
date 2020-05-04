<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\FeedbackForm;

interface FeedbackFormRepository
{

    public function ofId(string $firmId, string $feedbackFormId): FeedbackForm;

    public function all(string $firmId, int $page, int $pageSize);
}
