<?php

namespace GildedRoseTest;

use GildedRose\GildedRose;
use GildedRose\Item;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    public function testGildedRoseAgainstGoldenMaster()
    {
        // Given
        $items = $this->generateSampleOfItems();
        $itemsGoldenMaster = array_map(fn(Item $item) => clone $item, $items);

        // When
        (new GildedRose($items))->updateQuality();
        (new GildedRoseGoldenMaster($itemsGoldenMaster))->updateQuality();

        // Then
        foreach ($items as $key => $item) {
            $this->assertSame((string) $itemsGoldenMaster[$key], (string) $item);
        }
    }

    /**
     * @return Item[]
     */
    private function generateSampleOfItems(): array
    {
        $itemNames = [
            'Standard',
            'Aged Brie',
            'Backstage passes to a TAFKAL80ETC concert',
            'Sulfuras, Hand of Ragnaros',
        ];

        $items = [];
        foreach ($itemNames as $itemName) {
            for ($sellIn = 0; $sellIn < 100; $sellIn++) {
                for ($quality = 0; $quality < 100; $quality++) {
                    $items[] = new Item($itemName, $sellIn, $quality);
                }
            }
        }

        return $items;
    }
}
