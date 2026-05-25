<?php
class Sala{
    private $id;
   private $nome;
   private $max_partecipanti;
   private Palestra $palestra;

public function __construct($id, $nome, $max_partecipanti, $palestra) {
    $this->id = $id;
    $this->nome = $nome;
    $this->max_partecipanti = $max_partecipanti;
    $this->palestra = $palestra;
}
public function getId() {
    return $this->id;
}
public function get_nome(){
    return $this->nome;
}
public function get_max_partecipanti(){
    return $this->max_partecipanti;
}
public function get_palestra(){
    return $this->palestra;
}
public function set_nome($nome){
    $this->nome = $nome;
}
public function set_max_partecipanti($max_partecipanti){
    $this->max_partecipanti = $max_partecipanti;
}
public function set_palestra($palestra){
    $this->palestra = $palestra;
}
}