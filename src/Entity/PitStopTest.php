<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class PitStopTest extends TestEvent
{
    public function __construct(bool $longStop, int $score=20)
    {
        $this->score = $score;
        $this->fail = !$longStop && $score>10;
        $this->type = $longStop ? ConstantConstant::CST_LONG_STOP : ConstantConstant::CST_SHORT_STOP;
        $this->quantity = 1;
    }

    public function isLongStop(): bool
    {
        return $this->type==ConstantConstant::CST_LONG_STOP;
    }

    public function isFailedShortStop(): bool
    {
        return !$this->isLongStop() && $this->fail;
    }
    
}
