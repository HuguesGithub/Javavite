<?php
namespace src\Controller;

use src\Entity\BodyTest;
use src\Entity\BrakeEvent;
use src\Entity\DnfEvent;
use src\Entity\EngineTest;
use src\Entity\Event;
use src\Entity\FuelEvent;
use src\Entity\GearEvent;
use src\Entity\PitStopTest;
use src\Entity\StartTest;
use src\Entity\SuspensionTest;
use src\Entity\TaqEvent;
use src\Entity\TireEvent;
use src\Entity\TrailEvent;

class EventController extends GameController
{

    public static function getEventLi(Event $event): string
    {
        switch ($event::class) {
            case BrakeEvent::class :
                $returned = '<li class="bg-g0" title="Freinage">-</li>';
            break;
            case DnfEvent::class :
                $returned = '<li class="bg-g0" title="Abandon">X</li>';
            break;
            case FuelEvent::class :
                $returned = '<li class="bg-g0" title="Rétrogradation">'.$event->getQuantity().'</li>';
            break;
            case GearEvent::class :
                $returned = GearController::getGearLi($event);
            break;
            case TaqEvent::class :
                $returned = '<li class="bg-g0" title="Tête à queue">U</li>';
            break;
            case TireEvent::class :
                $returned = '<li class="bg-g0" title="Sortie de virage">-'.$event->getQuantity().'</li>';
            break;
            case TrailEvent::class :
                $returned = '<li class="bg-g0" title="Aspiration">+</li>';
            break;
            case BodyTest::class :
                $returned = '<li class="bg-g0" title="Carrosserie">'.$event->getScore().'</li>';
            break;
            case EngineTest::class :
                $returned = '<li class="bg-g0" title="Moteur">'.$event->getScore().'</li>';
            break;
            case PitStopTest::class :
                $returned = '<li class="bg-g0" title="Arrêt aux stands">¤</li>';
            break;
            case StartTest::class :
                $returned = '<li class="bg-g0" title="Départ">'.$event->getScore().'</li>';
            break;
            case SuspensionTest::class :
                $returned = '<li class="bg-g0" title="Tenue de route">'.$event->getScore().'</li>';
            break;
            default :
            $returned = '<li class="bg-g">X</li>';
            break;
        }
        return $returned;
    }

}
