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
            foreach ([0, 1, 5, 6, 11] as $sellIn) {
                foreach ([0, 49, 50] as $quality) {
                    $items[] = new Item($itemName, $sellIn, $quality);
                }
            }
        }

        return $items;
    }
}
