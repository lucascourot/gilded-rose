<?php

namespace GildedRose;

interface QualityCalculator
{
    public function updateQuality(Item $item): void;
}
