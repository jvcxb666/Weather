<?php

namespace App\Repository;

use App\Entity\Forecast;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Forecast>
 */
class ForecastRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Forecast::class);
    }

    public function findByDays(string $days): array
    {
        $dt = new DateTime();
        $start = date("Y-m-d H:i:s",strtotime($dt->format("Y-m-d")));
        $dt->modify("+ {$days} days");
        $end = date("Y-m-d H:i:s",strtotime($dt->format("Y-m-d")));

        return $this->getEntityManager()
            ->createQueryBuilder()
                ->select("f")
                    ->from("App\Entity\Forecast", "f")
                        ->andWhere("f.date >= '{$start}'")
                            ->andWhere("f.date < '{$end}'")
                                ->orderBy("f.date","ASC")
                                    ->getQuery()
                                        ->getArrayResult() 
                                            ?? [];
    }

    public function saveFromArray(string $type, array|null $array): void
    {
        if(empty($array)) return;

        $query = "INSERT INTO forecast (id,source,date,weather,temp,wind) VALUES";

        foreach($array as $item) {
            $query .= "(gen_random_uuid(),'{$type}','{$item['date']}','{$item['weather']}','{$item['temp']}','{$item['wind']}'),";
        }

        $this->getEntityManager()
            ->getConnection()
                ->executeQuery(rtrim($query,","));
    }

    public function clearData(string $source,int $days = 7): void
    {
        $dt = new DateTime();
        $start = date("Y-m-d H:i:s",strtotime($dt->format("Y-m-d")));
        $dt->modify("+ {$days} days");
        $end = date("Y-m-d H:i:s",strtotime($dt->format("Y-m-d")));

        $this->getEntityManager()
            ->createQueryBuilder()
                ->delete("App\Entity\Forecast", "f")
                    ->andWhere("f.source = '{$source}'")
                        ->andWhere("f.date >= '{$start}'")
                            ->andWhere("f.date < '{$end}'")
                                ->getQuery()
                                    ->execute();
    }
}
