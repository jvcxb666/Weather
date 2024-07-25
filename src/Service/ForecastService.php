<?php

namespace App\Service;

use App\Entity\Forecast;
use App\Parser\Factory\ParserFactory;
use App\Utils\RedisAdapter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Return_;

class ForecastService
{

    private const WEATHER_SITES = [
        "WorldWeather",
        "Gismeteo",
        "Gidromet"
    ];

    private EntityManagerInterface $em;
    private ServiceEntityRepository $repo;
    private RedisAdapter $cacher;

    public function __construct(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
        $this->repo = $this->em->getRepository(Forecast::class);
        $this->cacher = new RedisAdapter();
    }

    public function getActualNow(): array
    {
        $result = [];

        $now = date("Y-m-d H:i:s");
        foreach(self::WEATHER_SITES as $site) {
            $parser = ParserFactory::getParser($site);
            if($parser !== false) {
                $parsed = $parser->getNow();
                $parsed['source'] = $site;
                $parsed['date'] = $now;
                $data[] = $parsed;
            }
        }

        $result = $this->processData($data);
        $result['average'] = $this->getAverage($data);

        return $result ?? [];
    }

    public function getData(int $days = 7): array
    {
        $days = $days < 7 ? $days : 7;

        $rKey = "getWeather::{$days}";
        $rValue = $this->cacher->get($rKey);
        if(!empty($rValue)) return $rValue; 

        $data = $this->repo->findByDays($days);
        $result = [];

        $result = $this->processData($data);
        $result['average'] = $this->getAverage($data);
        unset($data);

        $this->cacher->set($rKey,$result);
        return $result ?? [];
    }

    public function updateData(): void
    {
        $data = [];

        $this->cacher->deleteByParts(["getWeather::"]);
        foreach(self::WEATHER_SITES as $site) {
            $parser = ParserFactory::getParser($site);
            if($parser !== false) $data[$site] = $parser->getWeek();
        }
        
        foreach($data as $source => $forecast) {
            $this->repo->clearData($source);
            $this->repo->saveFromArray($source,$forecast);
        }
    }
    
    private function processData(array $data): array
    {
        $result = [];

        foreach($data as $item) {
            $source = $item['source'];
            unset($item['source']);
            $item['date'] = is_string($item['date']) ? $item['date'] : $item['date']->format("Y-m-d H:i:s");;

            $result[$source][] = $item;
        }

        return $result;
    }

    private function getAverage(array $data): array
    {
        $result = [];

        foreach($data as $item) {
            $date = is_string($item['date']) ? $item['date'] : $item['date']->format("Y-m-d H:i:s");
            $result[$date]['date'] = $date;
            if(!empty($item['weather'])) $result[$date]['weather'][] = $item['weather'];
            if(!empty($item['temp'])) $result[$date]['temp'][] = $item['temp'];
            if(!empty($item['wind'])) $result[$date]['wind'][] = $item['wind'];
        }

        foreach($result as &$forecast) {
            $forecast['weather'] = array_unique($forecast['weather']);
            $forecast['temp'] = strval(floor(array_sum($forecast['temp'])/count($forecast['temp'])));
            $forecast['wind'] = strval(floor(array_sum($forecast['wind'])/count($forecast['wind'])));
            unset($forecast);
        }
        
        return array_values($result ?? []);
    }
}
