<?php

namespace App\Parser\Processor;

use DateTime;
use Exception;

class GidrometProcessor extends AbstractProcessor
{
    
        private const BASE_URL = "https://meteoinfo.ru/forecasts/russia/moscow-area/moscow";
        private const START_SEARCH = '<table border="0" width="100%" class="fc_tab_1">';
        private const END_SEARCH = 'Давление, мм рт.ст';
        private const START_NIGHT = 'Давление, мм рт.ст';
        private const END_NIGHT = '<div id="div_4_3"';

        public function getResult(string $date = ""): array
        {
            $url = self::BASE_URL.$date;
            $markup = $this->requestClient->sendRequest($url);
            if($markup === false) throw new Exception("Empty markup for {$url}");
            $this->result = $this->getResultStructure();
            $this->result['date'] = $this->generateDates(new DateTime(date("Y-m-d")));
            $day = $this->trimMarkup($markup,self::START_SEARCH,self::END_SEARCH);
            $night = $this->trimMarkup($markup,self::START_NIGHT,self::END_NIGHT);
            $this->parseMarkup($day);
            $this->parseMarkup($night);
            $this->generateResult();

            return $this->result;
        }

        public function parseMarkup(string $markup): bool
        {
            if(empty($markup)) return false;

            $array = explode("/td><td",$markup);

            foreach($array as $line) {
                $matches = [];
                if( str_contains($line,'class="fc_short_img"></div>') ) {
        
                    $regex = "/data-toggle=\"tooltip\" title=\"(.*?)\"/";
                    preg_match($regex,$line,$matches);
                    if(!empty($matches[1])) $this->result['weather'][] = $matches[1];
        
                } else if(str_contains($line,'&deg;" ><i>')) {
        
                    $regex = "/<i>(.*?)<\/i>&deg;<\/span>/";
                    $matches = [];
                    preg_match($regex,$line,$matches);
                    if(!empty($matches[1])) $this->result['temp'][] = $matches[1];
        
                } else if ( (str_contains($line,'<div class="fc_small_gorizont_ww"><i>') &&  str_contains($line,'м/c')) || str_contains($line,'><i> слабый</i>') ) {
        
                    if( str_contains($line,'><i> слабый</i>') ) {
                        $this->result['wind'][] = '0';
                    }else{
                        $regex = "/\s[0-9]*\s*-\s*[0-9]*\s/";
                        preg_match($regex,$line,$matches);
                        if(!empty($matches[0])) $this->result['wind'][] = str_replace(" ","",trim($matches[0]));
                    }
        
                }
                unset($matches);
            }
        
            return true;
        }

    protected function generateDates(DateTime $start, int $count = 7): array|false
    {
        $result = [];

        $day = clone $start;
        $day->modify("+6 hours");
        for($i = 0; $i < 7; $i++) {
            for($j = 0; $j < 5; $j++) {
                    $result[] = $day->modify("+3 hours")->format(self::DATETIME_FORMAT);
            }
            $day->modify("+ 9 hours");
        }
        unset($i,$j,$day);

        $night = clone $start;
        $night->modify("+ 21 hours");
        for($i = 0; $i < 6; $i++) {
                for($j = 0; $j < 3; $j++) {
                    $result[] = $night->modify("+3 hours")->format(self::DATETIME_FORMAT);
                }
                $night->modify("+ 15 hours");
            }
        return $result;
    }

    private function generateResult()
    {
        $result = [];
        for($i = 0; $i < 35; $i++) {
            $result[] = [
                "date" =>$this->result['date'][$i],
                "weather" => $this->result['weather'][$i/5],
                "temp" => $this->result['temp'][$i/5],
                "wind" => $this->result['wind'][$i/5],
            ];
        }
        $this->result = [
            "date" => array_slice($this->result['date'],35),
            "weather" => array_slice($this->result['weather'],7),
            "temp" => array_slice($this->result['temp'],7),
            "wind" => array_slice($this->result['wind'],7),
        ];
        for($i = 0; $i < count($this->result['date']); $i++) {
            $result[] = [
                "date" =>$this->result['date'][$i],
                "weather" => $this->result['weather'][$i/3] ?? null,
                "temp" => $this->result['temp'][$i/3] ?? null,
                "wind" => $this->result['wind'][$i/3] ?? null,
            ];
        }
        $this->result = $result;
    }
}
