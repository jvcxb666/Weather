<?php

namespace App\Parser\Processor;

use DateTime;
use Exception;

class WorldWeaterProcessor extends AbstractProcessor
{

    private const BASE_URL = "https://world-weather.ru/pogoda/russia/moscow/";
    private const START_SEARCH = '<table class="weather-today">';
    private const END_SEARCH = '<div id="defSet-3"';

    public function getResult(string $days = "7"): array
    {
        $url = self::BASE_URL;
        $markup = $this->requestClient->sendRequest($url);
        if($markup === false) throw new Exception("Empty markup for {$url}");
        $this->result = $this->getResultStructure();
        $this->result['date'] = $this->generateDates(new DateTime(date("Y-m-d")),intval($days));
        $markup = $this->trimMarkup($markup,self::START_SEARCH,self::END_SEARCH);
        $this->parseMarkup($markup);

        return $this->result;
    }

    public function parseMarkup(string $markup, array $result = []): bool
    {
        
        if(empty($markup)) return false;

        $array = explode("/td><td",$markup);

        foreach($array as $line) {
            $matches = [];
            if( str_contains($line,"class='weather-temperature'>") ) {
    
                $regex = "/title='(.*?)'/";
                preg_match($regex,$line,$matches);
                if(!empty($matches[1])) $this->result['weather'][] = trim($matches[1]);
    
                unset($regex);
                unset($matches);
    
                $regex = "/<span>(.*?)<\/span>/";
                $matches = [];
                preg_match($regex,$line,$matches);
                if(!empty($matches[1])) $this->result['temp'][] = str_replace("+","",substr(trim($matches[1]),0,-2));
    
            } else if ( str_contains($line,"class='weather-wind'") ) {
    
                $regex = "/class='tooltip '>(.*?)<\/span>/";
                preg_match($regex,$line,$matches);
                if(!empty($matches[1])) $this->result['wind'][] = trim($matches[1]);
    
            }
            unset($matches);
        }

        $result = [];
        for($i = 0; $i < count($this->result['date']); $i++) {
            for($j = 0; $j < 2; $j++){
                $result[] = [
                    "date" =>$this->result['date'][$i+$j],
                    "weather" => $this->result['weather'][$i/2],
                    "temp" => $this->result['temp'][$i/2],
                    "wind" => $this->result['wind'][$i/2],
                ];
            }
            $i++;
        }
        $this->result = $result;

        return true;
    }

    protected function generateDates(DateTime $start, int $count = 7): array|false
    {
        $result = [
            $start->format(self::DATETIME_FORMAT)
        ];

        for($i = 1; $i < $count * 8; $i++) {
            $start->modify("+ 3 hours");
            $result[] = $start->format(self::DATETIME_FORMAT);
        }

        return $result;
    }

}
