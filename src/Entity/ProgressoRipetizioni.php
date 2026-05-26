<?php
use Doctrine\ORM\Mapping as ORM;

class ProgressoRipetizioni extends Progresso{

    private int $new_ripetizioni;
    
    public function __construct(Cliente $cliente_riferito, int $new_ripetizioni, \DateTimeImmutable $data) {
        parent::__construct($cliente_riferito, $new_ripetizioni, $data);
        $this->new_ripetizioni = $new_ripetizioni;
    }

    public function getnew_ripetizioni(): int{
        return $this->new_ripetizioni;
    }
    public function setnew_ripetizioni(int $new_ripetizioni): self{
        $this->new_ripetizioni = $new_ripetizioni;
        return $this;
    }
}

?>