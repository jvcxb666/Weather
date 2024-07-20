<?php

namespace App\Parser\Parser;

use App\Parser\Interface\ParserInterface;
use App\Parser\Interface\ProcessorInterface;
use App\Parser\Processor\GidrometProcessor;
use DateTime;

class GidrometParser implements ParserInterface
{

    private ProcessorInterface $processor;

    public function __construct()
    {
        $this->processor = new GidrometProcessor();
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
        $weater = $this->processor->getResult();
        $date = date("Y-m-d");
        $result = [];

        foreach($weater as $item) {
            if(date("Y-m-d",strtotime($item['date'])) == $date) {
                $result[] = $item;
            }
        }

        return $result;
    }

    public function getWeek(): array
    {
        return $this->processor->getResult();
    }
}
