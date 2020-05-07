<?php

declare(strict_types=1);

namespace GildedRose;

interface QualityCalculator
{
    public function updateQuality(Item $item) : void;
}
