<?php
class Sala{
    private ?int $id = null;
   private string $nome;
   private int $max_partecipanti;
   private Palestra $palestra;

public function __construct(string $nome, int $max_partecipanti, Palestra $palestra) {
    $this->nome = $nome;
    $this->max_partecipanti = $max_partecipanti;
    $this->palestra = $palestra;
}
public function getId(): ?int {
    return $this->id;
}
public function getNome(): string{
    return $this->nome;
}
public function getMax_partecipanti(): int{
    return $this->max_partecipanti;
}
public function getPalestra(): Palestra{
    return $this->palestra;
}
public function setNome(string $nome): void{
    $this->nome = $nome;
}
public function setMax_partecipanti(int $max_partecipanti): void{
    $this->max_partecipanti = $max_partecipanti;
}
public function setPalestra(Palestra $palestra): void{
    $this->palestra = $palestra;
}
}
?>