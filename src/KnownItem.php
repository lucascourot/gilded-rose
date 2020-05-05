<?php

namespace GildedRose;

interface KnownItem extends QualityCalculator
{
    public static function name() : string;
}
