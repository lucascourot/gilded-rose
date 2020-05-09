<?php

declare(strict_types=1);

namespace GildedRose;

interface KnownItem extends QualityCalculator
{
    public const MAX_ITEM_QUALITY = 50;

    public static function name() : string;
}
