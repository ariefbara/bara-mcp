<?php

namespace Query\Application\Service\Firm;

use Query\Domain\Model\Firm\ClientCVForm;

interface ClientCVFormRepository
{

    public function aClientCVFormInFirm(string $firmId, string $clientCVFormId): ClientCVForm;

    public function allClientCVFormsInFirm(string $firmId, int $page, int $pageSize, ?bool $disableStatus);
}
