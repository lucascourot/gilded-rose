<?php

declare(strict_types=1);

namespace GildedRose;

final class DefaultItem implements QualityCalculator
{
    public function updateQuality(Item $item) : void
    {
        $item->sell_in--;

        if ($item->quality <= 0) {
            return;
        }

        $item->quality--;

        if ($item->sell_in >= 0 || $item->quality <= 0) {
            return;
        }

        $item->quality--;
    }
}
