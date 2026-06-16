<?php
namespace App\Control\DTO;

class AggiornaMisureDTO {
    public function __construct(
        public readonly float $peso,
        public readonly float $altezza,
        public readonly ?float $bicipiteDestro = null,
        public readonly ?float $bicipiteSinistro = null,
        public readonly ?float $tricipiteDestro = null,
        public readonly ?float $tricipiteSinistro = null,
        public readonly ?float $cosciaDestra = null,
        public readonly ?float $cosciaSinistra = null,
        public readonly ?float $polpaccioDestro = null,
        public readonly ?float $polpaccioSinistro = null,
        public readonly ?float $misuraPetto = null,
        public readonly ?float $misuraVita = null,
        public readonly ?float $misuraSpalle = null,
        public readonly ?float $misuraFianchi = null,
    ) {}
}