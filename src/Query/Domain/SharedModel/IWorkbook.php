<?php

namespace Query\Domain\SharedModel;

interface IWorkbook
{

    public function createSpreadsheet(): ISpreadsheet;
}
