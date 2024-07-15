<?php
namespace src\Entity;

use src\Constant\ConstantConstant;

class PitStopEvent extends Event
{
    private bool $longStop;
    private bool $failedShortStop;

    public function __construct(bool $longStop, bool $failedShortStop=false)
    {
        $this->type = '';
        $this->longStop = $longStop;
        $this->failed = $failedShortStop;
        $this->failedShortStop = $failedShortStop;
        $this->quantity = 1;
    }

    public function isLongStop(): bool
    {
        return $this->longStop;
    }

    public function isFailedShortStop(): bool
    {
        return $this->failedShortStop;
    }

}
