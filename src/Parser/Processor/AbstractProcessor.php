<?php

namespace App\Parser\Processor;

use App\Parser\Client\ParserRequestClient;
use App\Parser\Interface\ProcessorInterface;
use App\Parser\Interface\RequestClientInterface;
use DateTime;

abstract class AbstractProcessor implements ProcessorInterface
{

    protected const DATETIME_FORMAT = "Y-m-d H:i:s";
    protected RequestClientInterface $requestClient;
    protected array $result = [
        "date" => [],
        "weather" => [],
        "temp" => [],
        "wind" => [],
    ];


    public function __construct(RequestClientInterface|null $requestClientInterface = null)
    {
        if(empty($requestClientInterface)) $requestClientInterface = new ParserRequestClient();
        $this->requestClient = $requestClientInterface;
    }

    protected function trimMarkup($markup,string $start, string $end): string
    {
        $start_position = strpos($markup,$start);
        $end_position = (strlen($markup) - strpos($markup,$end)) * -1;

        return substr($markup,$start_position,$end_position);
    }

    protected function generateDates(DateTime $start, int $count = 7): array|false
    {
        $result = [
            $start->format(self::DATETIME_FORMAT)
        ];
        if($count < 2) return false;

        for($i = 1; $i < $count; $i++) {
            $start->modify("+ 1 day");
            $result[] = $start->format(self::DATETIME_FORMAT);
        }

        return $result;
    }

    public function getResultStructure(): array
    {
        return [
            "date" => [],
            "weather" => [],
            "temp" => [],
            "wind" => [],
        ];
    }
}
