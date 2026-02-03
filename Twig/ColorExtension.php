<?php

namespace Disjfa\TimetableBundle\Twig;

use Spatie\Color\Contrast;
use Spatie\Color\Hex;

class ColorExtension
{
    #[\Twig\Attribute\AsTwigFilter(name: 'contrast')]
    public function contrastColor($color): string
    {
        $light = Hex::fromString('#f8f9fa');
        $dark = Hex::fromString('#212529');

        $hex = Hex::fromString($color);

        if (Contrast::ratio($hex, $light) > Contrast::ratio($hex, $dark)) {
            return $light;
        }

        return $dark;
    }
}
