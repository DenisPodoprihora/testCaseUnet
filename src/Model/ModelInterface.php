<?php

namespace App\Model;

use Symfony\Component\HttpFoundation\ParameterBag;

interface ModelInterface
{
    public function createEntity(ParameterBag $data): bool;

    public function readEntity(int $entityId): array;

    public function updateEntity(int $entityId, ParameterBag $data): bool;

    public function deleteEntity(int $entityId): bool;

    public function getEntitiesForTable(): array;

    public function getEntityName(): string;


}