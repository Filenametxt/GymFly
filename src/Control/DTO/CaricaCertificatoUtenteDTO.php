<?php
namespace App\Control\DTO;

class CaricaCertificatoUtenteDTO {
    public function __construct(
        public readonly string $percorsoFileCertificato,
        public readonly string $dataScadenza
    ) {}
}