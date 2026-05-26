<?php
use Doctrine\ORM\Mapping as ORM;

class ProgressoRipetizioni extends Progresso{

    private int $new_durata;
    
    public function __construct(Cliente $cliente_riferito, int $new_durata, \DateTimeImmutable $data) {
        parent::__construct($cliente_riferito, $new_durata, $data);
        $this->new_durata = $new_durata;
    }

    public function getnew_durata(): int{
        return $this->new_durata;
    }
    public function setnew_durata(int $new_durata): self{
        $this->new_durata = $new_durata;
        return $this;
    }
}

?>