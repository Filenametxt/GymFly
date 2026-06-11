<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\ProgressoDurata;
use App\Entity\Repository\ProgressoDurataRepositoryInterface;

class DoctrineProgressoDurataRepository extends AbstractDoctrineProgressoRepository
    implements ProgressoDurataRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return ProgressoDurata::class;
    }

    public function save(ProgressoDurata $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}