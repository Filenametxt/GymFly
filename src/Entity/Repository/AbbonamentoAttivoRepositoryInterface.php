<?php

namespace App\Entity\Repository;

use App\Entity\AbbonamentoAttivo;
use App\Entity\Abbonamento;

interface AbbonamentoAttivoRepositoryInterface
{
    // -------------------------------------------------------------------------
    // CRUD base
    // -------------------------------------------------------------------------

    public function findById(int $id): ?AbbonamentoAttivo;
    public function save(AbbonamentoAttivo $abbonamentoAttivo): void;
    public function delete(AbbonamentoAttivo $abbonamentoAttivo): void;

    /** @return AbbonamentoAttivo[] */
    public function findAll(): array;

    // -------------------------------------------------------------------------
    // Metodi specifici del dominio
    // -------------------------------------------------------------------------

    /**
     * Trova tutti gli abbonamenti attivi riferiti a un certo tipo di Abbonamento.
     *
     * @return AbbonamentoAttivo[]
     */
    public function findByAbbonamento(Abbonamento $abbonamento): array;

    /**
     * Trova tutti gli abbonamenti attivi già scaduti (dataFine < oggi).
     * Utile per pulizie periodiche o notifiche di rinnovo.
     *
     * @return AbbonamentoAttivo[]
     */
    public function findScaduti(): array;

    /**
     * Trova tutti gli abbonamenti attivi che scadono entro N giorni.
     * Utile per inviare notifiche di rinnovo imminente.
     *
     * @return AbbonamentoAttivo[]
     */
    public function findInScadenza(int $giorni): array;

    /**
     * Trova tutti gli abbonamenti senza data di fine (es. abbonamenti open-end).
     * dataFine è nullable per supportare tipi futuri non a durata fissa.
     *
     * @return AbbonamentoAttivo[]
     */
    public function findSenzaDataFine(): array;
}