<?php

namespace GildedRoseTest;

use GildedRose\GildedRose;
use GildedRose\Item;
use GildedRose\KnownItems\AgedBrie;
use GildedRose\KnownItems\BackstagePasses;
use GildedRose\KnownItems\Conjured;
use GildedRose\KnownItems\Sulfuras;
use PHPUnit\Framework\TestCase;

class GildedRoseTest extends TestCase
{
    public function testGildedRoseAgainstGoldenMaster()
    {
        // Given
        $items = $this->generateSampleOfItemsForGoldenMaster();
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
    private function generateSampleOfItemsForGoldenMaster(): array
    {
        $itemNames = [
            'Standard',
            AgedBrie::name(),
            BackstagePasses::name(),
            Sulfuras::name(),
        ];

        $items = [];
        foreach ($itemNames as $itemName) {
            foreach ($this->sellInRange() as $sellIn) {
                foreach ($this->qualityRange() as $quality) {
                    $items[] = new Item($itemName, $sellIn, $quality);
                }
            }
        }

        return $items;
    }

    public function testConjuredItemsDegradeInQualityTwiceAsFastAsNormalItemsUntilQualityIs0()
    {
        foreach ($this->sellInRange() as $sellIn) {
            foreach ($this->qualityRange() as $quality) {
                // Given
                $conjuredItem = new Item(Conjured::name(), $sellIn, $quality);
                $normalItem = new Item('Normal', $sellIn, $quality);

                // When
                (new GildedRose([$normalItem, $conjuredItem]))->updateQuality();

                // Then
                $this->assertSame( $conjuredItem->sell_in, $normalItem->sell_in);
                $this->assertSame($sellIn - 1, $conjuredItem->sell_in);

                $normalDegradation = $quality - $normalItem->quality;
                $conjuredExpectedDegradation = max($quality - $normalDegradation * 2, 0);
                $this->assertSame(
                    $conjuredExpectedDegradation,
                    $conjuredItem->quality,
                    'for quality ' . $quality
                );
            }
        }
    }

    private function sellInRange(): array
    {
        return range(-5, 30);;
    }

    private function qualityRange(): array
    {
        return range(0, 50);
    }
}
