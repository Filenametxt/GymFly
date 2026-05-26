<?php
class CertificatoMedico{
    private ?int $id = null;
    private int $data_emissione; //REVIEW da capire il tipo di dato quando si passa al database
    private int $data_scadenza; //REVIEW da capire il tipo di dato quando si passa al database
    private string $medico;
    private $file; //REVIEW da capire il tipo di dato quando si passa al database
    private Cliente $cliente; //TODO

public function __construct(int $data_emissione, int $data_scadenza, string $medico, $file, Cliente $cliente) {
    $this->data_emissione = $data_emissione;
    $this->data_scadenza = $data_scadenza;
    $this->medico = $medico;
    $this->file = $file;
    $this->cliente = $cliente;
}
public function getId(): ?int {
    return $this->id;
}
public function getData_emissione(): int {
    return $this->data_emissione;
}
public function getData_scadenza(): int {
    return $this->data_scadenza;
}
public function getMedico(): string {
    return $this->medico;
}
public function getFile() { //REVIEW da capire il tipo di dato quando si passa al database
    return $this->file;
}
public function getCliente(): Cliente {
    return $this->cliente;
}
public function setData_emissione(int $data_emissione): void{
    $this->data_emissione = $data_emissione;
}
public function setData_scadenza(int $data_scadenza): void{
    $this->data_scadenza = $data_scadenza;
}
public function setMedico(string $medico): void{
    $this->medico = $medico;
}
public function setFile($file){ //REVIEW da capire il tipo di dato quando si passa al database
    $this->file = $file;
}
public function setCliente(Cliente $cliente): void{
    $this->cliente = $cliente;
}
}
?>
