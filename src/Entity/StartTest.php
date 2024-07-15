<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class StartTest extends TestEvent
{
    // Indique si le jet est un succès (notamment pour un super départ)
    protected bool $success;

    public function __construct(int $score)
    {
        $this->score = $score;
        $this->fail = $score==1;
        $this->success = $score==20;
        $this->type = '';
        $this->quantity = 1;
    }

    public function __toString(): string
    {
        $str = parent::__toString();
        return $str . ConstantConstant::CST_TAB.'success : '.($this->success?'true':'false').ConstantConstant::CST_EOL;
    }
    
}
