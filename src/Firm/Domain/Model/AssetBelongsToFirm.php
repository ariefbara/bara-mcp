<?php

namespace Firm\Domain\Model;

interface AssetBelongsToFirm
{
    public function belongsToFirm(Firm $firm): bool;
}
