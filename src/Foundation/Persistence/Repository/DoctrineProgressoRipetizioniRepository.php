<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\ProgressoRipetizioni;
use App\Entity\Repository\ProgressoRipetizioniRepositoryInterface;

class DoctrineProgressoRipetizioniRepository extends AbstractDoctrineProgressoRepository
    implements ProgressoRipetizioniRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return ProgressoRipetizioni::class;
    }

    public function save(ProgressoRipetizioni $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}