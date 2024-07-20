<?php

namespace App\Parser\Interface;

interface ParserInterface
{
    public function getNow(): array;
    public function getToday(): array;
    public function getWeek(): array;
}
