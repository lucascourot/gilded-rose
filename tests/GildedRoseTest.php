<?php

declare(strict_types=1);

namespace GildedRoseTest;

use GildedRose\GildedRose;
use GildedRose\Item;
use GildedRose\KnownItem;
use GildedRose\KnownItems\AgedBrie;
use GildedRose\KnownItems\BackstagePasses;
use GildedRose\KnownItems\Conjured;
use GildedRose\KnownItems\Sulfuras;
use PHPUnit\Framework\TestCase;
use function array_diff;
use function max;
use function range;

class GildedRoseTest extends TestCase
{
    public function testAllItemsExceptSulfurasDecreaseTheirSellIn() : void
    {
        // Given
        $allItemNamesExceptSulfuras = array_diff($this->allItems(), [Sulfuras::name()]);

        /** @var Item[] $items */
        $items = [];
        foreach ($allItemNamesExceptSulfuras as $itemName) {
            $items[] = new Item($itemName, 5, 10);
        }

        // When
        (new GildedRose($items))->updateQuality();

        // Then
        foreach ($items as $item) {
            $this->assertSame(4, $item->sell_in);
        }
    }

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
        /** @var Item[] $items */
        $items = [];
        foreach ($this->allItems() as $itemName) {
            foreach ($this->sellInRange() as $sellIn) {
                foreach ([0, 1, 2, 3] as $quality) {
                    $items[] = new Item($itemName, $sellIn, $quality);
                }
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

    public function testAgedBrieIncreasesTwiceFasterIfSellInDateHasPassed() : void
    {
        // Given
        $agedBrie = new Item(AgedBrie::name(), -5, 10);
        $maxQualityAgedBrie = new Item(AgedBrie::name(), -5, 49);
        $agedBrieSellIn1 = new Item(AgedBrie::name(), 1, 12);

        // When
        (new GildedRose([$agedBrie, $maxQualityAgedBrie, $agedBrieSellIn1]))->updateQuality();

        // Then
        $this->assertSame(12, $agedBrie->quality);
        $this->assertSame(KnownItem::MAX_ITEM_QUALITY, $maxQualityAgedBrie->quality);
        $this->assertSame(13, $agedBrieSellIn1->quality);
    }

    public function testQualityOfItemIsNeverMoreThan50() : void
    {
        // Given
        $maxQuality = KnownItem::MAX_ITEM_QUALITY;

        /** @var Item[] $items */
        $items = [];
        foreach ($this->allItems() as $itemName) {
            foreach ($this->sellInRange() as $sellIn) {
                $items[] = new Item($itemName, $sellIn, $maxQuality);
            }
        }

        // When
        (new GildedRose($items))->updateQuality();

        // Then
        foreach ($items as $item) {
            $this->assertLessThanOrEqual($maxQuality, $item->quality);
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

    public function testBackstagePassesIncreaseInQualityOverTime() : void
    {
        // Given
        $backstagePasses = new Item(BackstagePasses::name(), 11, 20);

        // When
        (new GildedRose([$backstagePasses]))->updateQuality();

        // Then
        $this->assertSame(21, $backstagePasses->quality);
    }

    public function testBackstagePassesIncreaseBy2WhenThereAre10daysOrLess() : void
    {
        // Given
        $backstagePasses6 = new Item(BackstagePasses::name(), 6, 20);
        $backstagePasses10 = new Item(BackstagePasses::name(), 10, 20);
        $backstagePassesMaxQuality = new Item(BackstagePasses::name(), 10, 49);

        // When
        (new GildedRose([$backstagePasses6, $backstagePasses10, $backstagePassesMaxQuality]))->updateQuality();

        // Then
        $this->assertSame(22, $backstagePasses6->quality);
        $this->assertSame(22, $backstagePasses10->quality);
        $this->assertSame(KnownItem::MAX_ITEM_QUALITY, $backstagePassesMaxQuality->quality);
    }

    public function testBackstagePassesIncreaseBy3WhenThereAre5daysOrLess() : void
    {
        // Given
        $backstagePasses1 = new Item(BackstagePasses::name(), 1, 20);
        $backstagePasses5 = new Item(BackstagePasses::name(), 5, 20);
        $backstagePassesMaxQuality = new Item(BackstagePasses::name(), 5, 48);

        // When
        (new GildedRose([$backstagePasses1, $backstagePasses5, $backstagePassesMaxQuality]))->updateQuality();

        // Then
        $this->assertSame(23, $backstagePasses1->quality);
        $this->assertSame(23, $backstagePasses5->quality);
        $this->assertSame(KnownItem::MAX_ITEM_QUALITY, $backstagePassesMaxQuality->quality);
    }

    public function testQualityDropsTo0AfterTheConcert() : void
    {
        // Given
        $backstagePasses = new Item(BackstagePasses::name(), 0, 30);

        // When
        (new GildedRose([$backstagePasses]))->updateQuality();

        // Then
        $this->assertSame(0, $backstagePasses->quality);
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

    public function testItemsCanConvertToString() : void
    {
        $item = new Item('Normal', 5, 10);

        $this->assertSame('Normal, 5, 10', (string) $item);
    }

    /**
     * @return array<string>
     */
    private function allItems() : array
    {
        return [
            'Normal',
            AgedBrie::name(),
            BackstagePasses::name(),
            Sulfuras::name(),
            Conjured::name(),
        ];
    }

    /**
     * @return array<int>
     */
    private function sellInRange() : array
    {
        return range(-5, 20);
    }

    /**
     * @return array<int>
     */
    private function qualityRange() : array
    {
        return range(0, KnownItem::MAX_ITEM_QUALITY);
    }
}
