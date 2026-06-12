<?php

namespace App\Foundation\Persistence\Repository;

use App\Entity\ProgressoCarico;
use App\Entity\Repository\ProgressoCaricoRepositoryInterface;

class DoctrineProgressoCaricoRepository extends AbstractDoctrineProgressoRepository
    implements ProgressoCaricoRepositoryInterface
{
    protected function getEntityClass(): string
    {
        return ProgressoCarico::class;
    }

    public function save(ProgressoCarico $entity): void
    {
        $this->em->persist($entity);
        $this->em->flush();
    }
}