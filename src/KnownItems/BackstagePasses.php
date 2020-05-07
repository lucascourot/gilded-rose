<?php

declare(strict_types=1);

namespace GildedRose\KnownItems;

use GildedRose\Item;
use GildedRose\KnownItem;

final class BackstagePasses implements KnownItem
{
    private const MAX_QUALITY = 50;

    public static function name() : string
    {
        return 'Backstage passes to a TAFKAL80ETC concert';
    }

    public function updateQuality(Item $item) : void
    {
        $item->sell_in--;

        if ($item->quality >= self::MAX_QUALITY && $item->sell_in >= 0) {
            return;
        }

        if ($item->sell_in < 0) {
            $item->quality = 0;

            return;
        }

        $item->quality++;

        if ($item->sell_in < 10 && $item->quality < self::MAX_QUALITY) {
            $item->quality++;
        }

        if ($item->sell_in >= 5 || $item->quality >= self::MAX_QUALITY) {
            return;
        }

        $item->quality++;
    }
}
