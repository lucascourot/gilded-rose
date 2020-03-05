<?php

namespace GildedRoseTest;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    public function testGildedRose()
    {
        $agedBrie = new Item('Aged Brie', 1, 1);
        $gildedRose = new GildedRose([$agedBrie]);

        $gildedRose->updateQuality();

        $this->assertSame('Aged Brie', $agedBrie->name);
        $this->assertSame(0, $agedBrie->sell_in);
        $this->assertSame(2, $agedBrie->quality);
    }
}
