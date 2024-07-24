<?php

namespace App\Parser\Processor;

use DateTime;
use Exception;

class GisMeteoProcessor extends AbstractProcessor
{
    private const BASE_URL = "https://www.gismeteo.ru/weather-moscow-4368";
    private const START_SEARCH = 'class="widget-row widget-row-datetime-time"';
    private const END_SEARCH = '<div class="widget-weather-parameters-footer">';

    public function getResult(string $date = "/"): array
    {
        $url = self::BASE_URL.$date;
        $markup = $this->requestClient->sendRequest($url);
        if($markup === false) throw new Exception("Empty markup for {$url}");
        $this->result = $this->getResultStructure();
        $markup = $this->trimMarkup($markup,self::START_SEARCH,self::END_SEARCH);
        $this->parseMarkup($markup);

        return $this->result;
    }

    public function parseMarkup(string $markup): bool
    {
        if(empty($markup)) return false;
        $array = explode("</div>",$markup);

        foreach($array as $line) {
            $matches = [];
            if( str_contains($line,"Фактические данные от") || str_contains($line,"Прогноз от") ) {

                $i = 1;
                $regex = "/UTC\),\s(.*?)\s\(UTC/";
                preg_match($regex,$line,$matches);
                if(empty($matches)) {
                    $regex = "/Фактические данные от:\s*(.*?)\s*\(/";
                    preg_match($regex,$line,$matches);
                    $i = 1;
                }
                if(!empty($matches[$i])) $this->result['date'][] = trim($matches[$i]);

            } else if ( str_contains($line,'class="row-item" data-tooltip=') ) {

                $regex = "/data-tooltip=\"(.*?)\"/si";
                preg_match($regex,$line,$matches);
                if(!empty($matches[1]) && count($this->result['weather']) < 8) $this->result['weather'][] = trim($matches[1]);

            } else if ( str_contains($line,"temperature-value") ) {

                $regex = "/temperature-value value=\"(.*?)\"/";
                preg_match($regex,$line,$matches);
                if(!empty($matches[1]) && count($this->result['temp']) < 8) $this->result['temp'][] = trim(strip_tags($matches[1]));

            } else if ( str_contains($line,"speed-value") && !str_contains($line,"data-row")) {

                $regex = "/speed-value value=\"(.*?)\"/";
                preg_match($regex,$line,$matches);
                if(isset($matches[1])) $this->result['wind'][] = trim($matches[1]);

            }
            unset($matches);
        }

        $result = [];
        for($i = 0; $i < count($this->result['date']); $i++) {
            $date = new DateTime($this->result['date'][$i]);
            $result[] = [
                "date" => $date->modify("+3 hours")->format(self::DATETIME_FORMAT),
                "weather" => $this->result['weather'][$i],
                "temp" => $this->result['temp'][$i],
                "wind" => $this->result['wind'][$i],
            ];
        }
        $this->result = $result;

        return true;
    }

}
