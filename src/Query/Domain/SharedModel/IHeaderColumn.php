<?php

namespace Query\Domain\SharedModel;

interface IHeaderColumn
{

    public function getColNumber(): int;

    public function getLabel(): string;

    public function toArray(): array;
}
