<?php
use Doctrine\ORM\Mapping as ORM;

class ProgressoRipetizioni extends Progresso{

    private int $new_carico;
    
    public function __construct(Cliente $cliente_riferito, int $new_carico, \DateTimeImmutable $data) {
        parent::__construct($cliente_riferito, $new_carico, $data);
        $this->new_carico = $new_carico;
    }

    public function getnew_carico(): int{
        return $this->new_carico;
    }
    public function setnew_carico(int $new_carico): self{
        $this->new_carico = $new_carico;
        return $this;
    }
}

?>