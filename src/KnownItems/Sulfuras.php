<?php

namespace GildedRose\KnownItems;

use GildedRose\Item;
use GildedRose\KnownItem;

final class Sulfuras implements KnownItem
{
    public static function name(): string
    {
        return 'Sulfuras, Hand of Ragnaros';
    }

    public function updateQuality(Item $item) : void
    {
        // NoOp
    }
}
