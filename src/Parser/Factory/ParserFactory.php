<?php

namespace App\Parser\Factory;

use App\Parser\Interface\ParserInterface;
use App\Parser\Parser\GidrometParser;
use Parser\Parser\GisMeteoParser;
use Parser\Parser\WorldWeatherParser;

class ParserFactory
{

    public static function getParser(string $type): ParserInterface|false
    {
        switch(strtolower($type)) {
            case "gidromet":
                return new GidrometParser();
            case "gismeteo":
                return new GisMeteoParser();
            case "worldweather":
                return new WorldWeatherParser();
            default:
                return false;
        }
    }

}
