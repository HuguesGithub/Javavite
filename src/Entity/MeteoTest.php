<?php
namespace src\Entity;

class MeteoTest extends TestEvent
{

    public function __construct(int $score, string $type)
    {
        $this->score = $score;
        $this->fail = false;
        $this->quantity = 1;
        $this->type = $type;
    }
    
}
