<?php

require dirname(__DIR__).'/vendor/autoload.php';

use GildedRose\Item;
use GildedRose\GildedRose;
use Symfony\Component\Console\Output\OutputInterface;

$items = [
    new Item('Aged Brie', 5, 10),
    new Item('Backstage passes to a TAFKAL80ETC concert', 5, 10),
    new Item('Sulfuras, Hand of Ragnaros', 5, 10),
];

$app = new Silly\Application();
$app->command('run', function (OutputInterface $output) use ($items) {
    $gildedRose = new GildedRose($items);
    $gildedRose->updateQuality();

    /** @var Item[] $items */
    foreach ($items as $item) {
        $output->writeln('-> ' . (string) $item);
    }
});
$app->run();
