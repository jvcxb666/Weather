<?php

namespace Parser\Parser;

use App\Parser\Interface\ParserInterface;
use App\Parser\Interface\ProcessorInterface;
use App\Parser\Processor\GisMeteoProcessor;
use DateTime;

class GisMeteoParser implements ParserInterface
{
    private ProcessorInterface $processor;
    private const DATES = [
        "today" => "/",
        "+1" => "/tomorrow/",
        "+2" => "/3-day/",
        "+3" => "/4-day/",
        "+4" => "/5-day/",
        "+5" => "/6-day/",
        "+6" => "/7-day/",
    ];

    public function __construct()
    {
        $this->processor = new GisMeteoProcessor();
    }

    public function getNow(): array
    {
        $date = new DateTime();
        $date->modify("+ 3 hours");
        $weater = $this->processor->getResult();

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
        return $this->processor->getResult();
    }

    public function getWeek(): array
    {
        $result = [];

        foreach(self::DATES as $date) {
           foreach($this->processor->getResult($date) as $item) {
                $result[] = $item;
           }
        }

        return $result;
    }
}
