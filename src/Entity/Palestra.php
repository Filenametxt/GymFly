<?php
class Palestra {
    private $nome;
    private $indirizzo;
    private $email;
    private $recapito_telefonico;

    public function __construct($nome, $indirizzo, $email, $recapito_telefonico){
        $this->nome = $nome;
        $this->indirizzo = $indirizzo;
        $this->email = $email;
        $this->recapito_telefonico = $recapito_telefonico;
    }

    public function get_nome() {
        return $this->nome;
    }
    
    public function get_indirizzo() {
        return $this->indirizzo;
    }

    public function get_email() {
        return $this->email;
    }

    public function get_recapito_telefonico($recapito_telefonico){
        return $this->recapito_telefonico;
    }

    public function set_nome($nome){
        $this->nome = $nome;
    }

    public function set_indirizzo($indirizzo){
        $this->indirizzo = $indirizzo;
    }

    public function set_email($email){
        $this->email = $email;
    }

    public function set_recapito_telefonico($recapito_telefonico){
        $this->recapito_telefonico = $recapito_telefonico;
    }

}
?>
