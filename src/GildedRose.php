<?php

declare(strict_types=1);

namespace GildedRose;

use GildedRose\KnownItems\AgedBrie;
use GildedRose\KnownItems\BackstagePasses;
use GildedRose\KnownItems\Conjured;
use GildedRose\KnownItems\Sulfuras;

final class GildedRose
{
    /** @var Item[] */
    private array $itemsToUpdate = [];

    /** @var array<string, KnownItem> $knownItems QualityCalculator indexed by itemName */
    private array $knownItems = [];

    /**
     * @param array<Item> $itemsToUpdate
     */
    public function __construct(array $itemsToUpdate)
    {
        $this->itemsToUpdate = $itemsToUpdate;
        $this->knownItems = [
            AgedBrie::name() => new AgedBrie(),
            BackstagePasses::name() => new BackstagePasses(),
            Sulfuras::name() => new Sulfuras(),
            Conjured::name() => new Conjured(),
        ];
    }

    public function updateQuality() : void
    {
        foreach ($this->itemsToUpdate as $itemToUpdate) {
            $this->findQualityCalculatorForItem($itemToUpdate->name)->updateQuality($itemToUpdate);
        }
    }

    private function findQualityCalculatorForItem(string $itemName) : QualityCalculator
    {
        return $this->knownItems[$itemName] ?? new DefaultItem();
    }
}
