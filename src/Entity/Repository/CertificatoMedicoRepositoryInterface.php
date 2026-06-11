<?php

namespace App\Entity\Repository;

use App\Entity\CertificatoMedico;
use App\Entity\Cliente;

interface CertificatoMedicoRepositoryInterface
{
    // --- CRUD base ---

    public function findById(int $id): ?CertificatoMedico;

    public function save(CertificatoMedico $entity): void;

    public function delete(CertificatoMedico $entity): void;

    /** @return CertificatoMedico[] */
    public function findAll(): array;

    // --- Metodi di dominio ---

    /**
     * Restituisce il certificato attualmente associato a un cliente.
     * Un cliente ha un solo certificato attivo per volta.
     */
    public function findByCliente(Cliente $cliente): ?CertificatoMedico;

    /**
     * Restituisce tutti i certificati scaduti (dataScadenza < oggi).
     *
     * @return CertificatoMedico[]
     */
    public function findScaduti(): array;

    /**
     * Restituisce i certificati in scadenza entro $giorni giorni da oggi.
     * Utile per inviare notifiche preventive ai clienti.
     *
     * @return CertificatoMedico[]
     */
    public function findInScadenzaEntro(int $giorni): array;

    /**
     * Restituisce tutti i certificati ancora validi (dataScadenza >= oggi).
     *
     * @return CertificatoMedico[]
     */
    public function findValidi(): array;

    /**
     * Verifica se un cliente possiede un certificato ancora valido.
     * Usato prima di consentire iscrizioni ad attività o sessioni private.
     */
    public function clienteHaCertificatoValido(Cliente $cliente): bool;
}