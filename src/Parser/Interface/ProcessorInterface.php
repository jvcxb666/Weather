<?php

namespace App\Parser\Interface;

interface ProcessorInterface
{
    public function parseMarkup(string $markup): bool;
    public function getResult(string $url = ""): array;
    public function getResultStructure(): array;
}
