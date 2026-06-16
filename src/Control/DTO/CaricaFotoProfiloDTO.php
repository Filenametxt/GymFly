<?php
namespace App\Control\DTO;

class CaricaFotoProfiloDTO {
    public function __construct(
        public readonly string $percorsoFileFoto // La View ha già salvato il file e passa il path
    ) {}
}