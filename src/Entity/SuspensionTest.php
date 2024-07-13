<?php
namespace src\Entity;

class SuspensionTest extends TestEvent
{

    public function __construct(int $score, string $requis)
    {
        $seuil = substr($requis, 1);

        $this->score = $score;
        $this->fail = $score<=$seuil;
        $this->type = '';
        $this->quantity = 1;
    }
    
}
