<?php
namespace src\Entity;

class BodyTest extends TestEvent
{

    public function __construct(int $score, string $requis)
    {
        $seuil = substr($requis, 1, 1);
        if (strpos($requis, 'mouillée')!==false) {
            $seuil++;
        }
        if (strpos($requis, 'aggravé')!==false) {
            $seuil++;
        }

        $this->score = $score;
        $this->fail = $score<=$seuil;
        $this->quantity = 1;
        $this->type = '';
    }
    
}
