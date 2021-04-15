<?php

namespace Query\Application\Service\Personnel;

interface RegistrantRepository
{

    public function allRegistrantsAccessibleByPersonnel(
            string $personnelId, int $page, int $pageSize, ?bool $concludedStatus);
}
