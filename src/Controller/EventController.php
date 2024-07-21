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
        $returned = '<li class="bg-g0" title="%s">%s</li>';
        switch ($event::class) {
            case BrakeEvent::class :
                $params = ['Freinage', '-'];
            break;
            case DnfEvent::class :
                $params = ['Abandon', 'X'];
            break;
            case FuelEvent::class :
                $params = ['Rétrogradation', $event->getQuantity()];
            break;
            case GearEvent::class :
                return GearController::getGearLi($event);
            break;
            case TaqEvent::class :
                $params = ['Tête à queue', 'U'];
            break;
            case TireEvent::class :
                $params = ['Sortie de virage', '-'.$event->getQuantity()];
            break;
            case TrailEvent::class :
                $params = ['Aspiration', '+'];
            break;
            case BodyTest::class :
                $params = ['Carrosserie', $event->getScore()];
            break;
            case EngineTest::class :
                $params = ['Moteur', $event->getScore()];
            break;
            case PitStopTest::class :
                $params = ['Arrêt aux stands', '¤'];
            break;
            case StartTest::class :
                $params = ['Départ', $event->getScore()];
            break;
            case SuspensionTest::class :
                $params = ['Tenue de route', $event->getScore()];
            break;
            default :
                $params = ['Non défini', '?'];
            break;
        }
        return vsprintf($returned, $params);
    }

}
