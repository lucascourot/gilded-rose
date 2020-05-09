<?php

declare(strict_types=1);

namespace GildedRoseTest;

use GildedRose\GildedRose;
use GildedRose\Item;
use GildedRose\KnownItems\AgedBrie;
use GildedRose\KnownItems\BackstagePasses;
use GildedRose\KnownItems\Conjured;
use GildedRose\KnownItems\Sulfuras;
use PHPUnit\Framework\TestCase;
use function array_map;
use function max;
use function range;

class GildedRoseTest extends TestCase
{
    public function testOnceTheSellByDateHasPassedForNormalItemQualityDegradesTwiceAsFast() : void
    {
        // Given
        $quality = 5;
        $normalItem = new Item('Normal', 1, $quality);
        $normalItemSellByDateHasPassed = new Item('Normal', 0, $quality);

        // When
        (new GildedRose([$normalItem, $normalItemSellByDateHasPassed]))->updateQuality();

        // Then
        $normalItemQualityDecreasedBy = $quality - $normalItem->quality;
        $this->assertSame($quality - $normalItemQualityDecreasedBy * 2, $normalItemSellByDateHasPassed->quality);
    }

    public function testTheQualityOfAnItemIsNeverNegative() : void
    {
        // Given
        $quality = 0;
        $itemNames = [
            'Normal',
            AgedBrie::name(),
            BackstagePasses::name(),
            Sulfuras::name(),
            Conjured::name(),
        ];

        /** @var Item[] $items */
        $items = [];
        foreach ($itemNames as $itemName) {
            foreach ($this->sellInRange() as $sellIn) {
                $items[] = new Item($itemName, $sellIn, $quality);
            }
        }

        // When
        (new GildedRose($items))->updateQuality();

        // Then
        foreach ($items as $item) {
            $this->assertGreaterThanOrEqual(0, $item->quality);
        }
    }

    public function testAgedBrieIncreasesInQualityOverTime() : void
    {
        // Given
        $agedBrie = new Item(AgedBrie::name(), 5, 10);

        // When
        (new GildedRose([$agedBrie]))->updateQuality();

        // Then
        $this->assertSame(11, $agedBrie->quality);
    }

    public function testQualityOfItemIsNeverMoreThan50() : void
    {
        // Given
        $maxQuality = 50;
        $itemNames = [
            'Normal',
            AgedBrie::name(),
            BackstagePasses::name(),
            Sulfuras::name(),
            Conjured::name(),
        ];

        /** @var Item[] $items */
        $items = [];
        foreach ($itemNames as $itemName) {
            foreach ($this->sellInRange() as $sellIn) {
                $items[] = new Item($itemName, $sellIn, $maxQuality);
            }
        }

        // When
        (new GildedRose($items))->updateQuality();

        // Then
        foreach ($items as $item) {
            $this->assertLessThanOrEqual(50, $item->quality);
        }
    }

    public function testSulfurasNeverHasToBeSoldOrDecreasedInQuality() : void
    {
        // Given
        $sulfurasItem = new Item(Sulfuras::name(), 10, 15);

        // When
        (new GildedRose([$sulfurasItem]))->updateQuality();

        // Then
        $this->assertSame(10, $sulfurasItem->sell_in);
        $this->assertSame(15, $sulfurasItem->quality);
    }

    public function testConjuredItemsDegradeInQualityTwiceAsFastAsNormalItemsUntilQualityIs0() : void
    {
        foreach ($this->sellInRange() as $sellIn) {
            foreach ($this->qualityRange() as $quality) {
                // Given
                $conjuredItem = new Item(Conjured::name(), $sellIn, $quality);
                $normalItem = new Item('Normal', $sellIn, $quality);

                // When
                (new GildedRose([$normalItem, $conjuredItem]))->updateQuality();

                // Then
                $this->assertSame($conjuredItem->sell_in, $normalItem->sell_in);
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

    public function testGildedRoseAgainstGoldenMaster() : void
    {
        // Given
        $items = $this->generateSampleOfItemsForGoldenMaster();

        $itemsGoldenMaster = array_map(static fn(Item $item) => clone $item, $items);

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
    private function generateSampleOfItemsForGoldenMaster() : array
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

    /**
     * @return array<int>
     */
    private function sellInRange() : array
    {
        return range(-5, 30);
    }

    /**
     * @return array<int>
     */
    private function qualityRange() : array
    {
        return range(0, 80);
    }
}
