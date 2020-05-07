<?php

declare(strict_types=1);

namespace GildedRose\KnownItems;

use GildedRose\Item;
use GildedRose\KnownItem;

final class AgedBrie implements KnownItem
{
    private const MAX_QUALITY = 50;

    public static function name() : string
    {
        return 'Aged Brie';
    }

    public function updateQuality(Item $item) : void
    {
        $item->sell_in--;

        if ($item->quality >= self::MAX_QUALITY) {
            return;
        }

        $item->quality++;

        if ($item->sell_in >= 0 || $item->quality >= self::MAX_QUALITY) {
            return;
        }

        $item->quality++;
    }
}
