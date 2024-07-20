<?php

namespace App\Entity;

use App\Repository\ForecastRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;

#[ORM\Entity(repositoryClass: ForecastRepository::class)]
#[ORM\Index("source_idx",["source"])]
#[ORM\Index("date_idx",["date"])]
class Forecast
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    #[ORM\GeneratedValue(strategy:"CUSTOM")]
    #[ORM\CustomIdGenerator(class:UuidGenerator::class)]
    private ?string $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $weather = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $temp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $wind = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getWeather(): ?string
    {
        return $this->weather;
    }

    public function setWeather(?string $weather): static
    {
        $this->weather = $weather;

        return $this;
    }

    public function getTemp(): ?string
    {
        return $this->temp;
    }

    public function setTemp(?string $temp): static
    {
        $this->temp = $temp;

        return $this;
    }

    public function getWind(): ?string
    {
        return $this->wind;
    }

    public function setWind(?string $wind): static
    {
        $this->wind = $wind;

        return $this;
    }
}
