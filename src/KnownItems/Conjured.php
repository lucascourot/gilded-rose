<?php

declare(strict_types=1);

namespace GildedRose\KnownItems;

use GildedRose\Item;
use GildedRose\KnownItem;

final class Conjured implements KnownItem
{
    public static function name() : string
    {
        return 'Conjured';
    }

    public function updateQuality(Item $item) : void
    {
        $item->sell_in--;

        if ($item->quality <= 0) {
            return;
        }

        $item->quality--;

        $this->decreaseQualityByOneUntilZero($item);

        if ($item->sell_in >= 0) {
            return;
        }

        $this->decreaseQualityByOneUntilZero($item);
        $this->decreaseQualityByOneUntilZero($item);
    }

    private function decreaseQualityByOneUntilZero(Item $item) : void
    {
        if ($item->quality <= 0) {
            return;
        }

        $item->quality--;
    }
}
