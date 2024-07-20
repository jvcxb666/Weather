<?php

namespace Parser\Parser;

use App\Parser\Interface\ParserInterface;
use App\Parser\Interface\ProcessorInterface;
use App\Parser\Processor\WorldWeaterProcessor;
use DateTime;

class WorldWeatherParser implements ParserInterface
{
    private ProcessorInterface $processor;

    public function __construct()
    {
        $this->processor = new WorldWeaterProcessor();
    }

    public function getNow(): array
    {
        $date = new DateTime();
        $date->modify("+ 3 hours");
        $weater = $this->processor->getResult(1);

        switch($date->format("H") % 3){
            case 1:
                $date->modify("- 1 hours");
                break;
            case 2:
                $date->modify("+ 1 hours");
                break;
            default:
                break;
        }

        $search = $date->format("Y-m-d H");

        foreach($weater as $item) {
            if(date("Y-m-d H",strtotime($item['date'])) == $search) {
                $now = new DateTime();
                $item['date'] = $now->modify("+ 3 hours")->format("Y-m-d H:i:s");
                return $item;
            }
        }

        return [];
    }

    public function getToday(): array
    {
        return $this->processor->getResult(1);
    }

    public function getWeek(): array
    {
       return $this->processor->getResult(7);
    }
}
