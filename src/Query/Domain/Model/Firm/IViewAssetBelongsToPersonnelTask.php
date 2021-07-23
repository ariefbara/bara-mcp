<?php

namespace Query\Domain\Model\Firm;

interface IViewAssetBelongsToPersonnelTask
{
    public function viewAssetBelongsToPersonnel(string $personnelId): array;
}
