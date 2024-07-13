<?php
namespace src\Entity;

class EngineTest extends TestEvent
{

    public function __construct(int $score, string $requis)
    {
        $seuil = substr($requis, 1);

        $this->score = $score;
        $this->fail = $score<=$seuil;
        $this->quantity = 1;
        $this->type = '';
    }
    
}
