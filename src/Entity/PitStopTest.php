<?php
namespace src\Entity;

class PitStopTest extends Test
{

    public function __construct(int $score)
    {
        $this->score = $score;
        $this->fail = $score>10;
    }
    
}
