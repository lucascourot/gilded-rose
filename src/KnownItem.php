<?php

declare(strict_types=1);

namespace GildedRose;

interface KnownItem extends QualityCalculator
{
    public static function name() : string;
}
