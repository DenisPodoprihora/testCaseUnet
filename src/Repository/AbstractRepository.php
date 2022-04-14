<?php

namespace App\Repository;

use App\Entity\EntityInterface;
use App\Repository\Exception\CannotBuildRepositoryException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

class AbstractRepository extends ServiceEntityRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected string $lastError;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param ManagerRegistry $registry
     * @param LoggerInterface $logger
     * @throws CannotBuildRepositoryException
     */
    public function __construct(
        ManagerRegistry $registry,
        LoggerInterface $logger
        )
    {
        $this->logger = $logger;
        parent::__construct($registry, $this->getEntityName());
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return parent::getEntityManager();
    }

    /**
     * @param ...$params
     * @return EntityInterface|null
     */
    public function getEntityObject(...$params): ?EntityInterface
    {
        $entity = null;
        $criteria = $params[0] ?? null;
        $returnObjectAnyway = $params[1] ?? false;

        if (null === $criteria) {
            $entity = new $this->_entityName;
        }

        if (\is_int($criteria)) {
            $entity = $this->_em->getRepository($this->_entityName)->find($criteria);
        }

        if (\is_array($criteria)) {
            $entity = $this->_em->getRepository($this->_entityName)->findOneBy($criteria);
        }

        return $entity ?? ($returnObjectAnyway ? new $this->_entityName : null);
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function delete(EntityInterface $entity): bool
    {
        try {
            $this->_em->remove($entity);
            $this->_em->flush();
            $result = true;
        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            $this->lastError = $ex->getMessage();
            $result = false;
        }

        return $result;
    }

    /**
     * @param EntityInterface $entity
     * @return bool
     */
    public function save(EntityInterface $entity): bool
    {
        try {
            $this->_em->persist($entity);
            $this->_em->flush($entity);
            $result = true;
        } catch (\Exception $ex) {
            dump($entity);
            dd($ex);
            $this->logger->error($ex->getMessage());
            $this->lastError = $ex->getMessage();
            $result = false;
        }

        return $result;
    }

    /**
     * @return string|null
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    /**
     * @return string
     * @throws CannotBuildRepositoryException
     */
    protected function getEntityName(): string
    {
        $repositoryNameParts = explode('\\', \get_class($this));
        $repositoryName = end($repositoryNameParts);

        if (!str_contains($repositoryName, 'Repository')) {
            throw new CannotBuildRepositoryException(' Non-standard repository name given. Repository must be named like "EntityNameRepository"');
        }

        $entityNameFull = 'App\\Entity\\'.implode(array_map('ucfirst', explode('-', str_replace('Repository', '', $repositoryName))));

        if (!class_exists($entityNameFull)) {
            throw new CannotBuildRepositoryException(" Entity \"{$entityNameFull}\" does not exist");
        }

        return $entityNameFull;
    }

    /**
     * @param string $alias
     * @return array
     */
    public function getAllRows(string $alias): array
    {
        return $this->createQueryBuilder($alias)->getQuery()->getArrayResult();
    }
}