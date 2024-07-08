<?php
namespace src\Entity;

class StartTest extends Test
{
    // Indique si le jet est un succès (notamment pour un super départ)
    protected bool $success;

    public function __construct(int $score)
    {
        $this->score = $score;
        $this->fail = $score==1;
        $this->success = $score==20;
    }
    
}
