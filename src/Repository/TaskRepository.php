<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class TaskRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry, LoggerInterface $logger)
    {
        parent::__construct($registry, $logger);
    }
}