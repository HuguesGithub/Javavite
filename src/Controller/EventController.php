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
        $returned = '<li class="bg-g%s" title="%s">%s</li>';
        switch ($event::class) {
            case BrakeEvent::class :
                $params = ['0', 'Freinage', '-'];
            break;
            case DnfEvent::class :
                $params = ['0', 'Abandon', 'X'];
            break;
            case FuelEvent::class :
                $params = ['0', 'Rétrogradation', $event->getQuantity()];
            break;
            case GearEvent::class :
                $params = [$event->getType(), 'Vitesse', $event->getScore()];
            break;
            case TaqEvent::class :
                $params = ['0', 'Tête à queue', 'U'];
            break;
            case TireEvent::class :
                $params = ['0', 'Sortie de virage', '-'.$event->getQuantity()];
            break;
            case TrailEvent::class :
                $params = ['0', 'Aspiration', '+'];
            break;
            case BodyTest::class :
                $params = ['0', 'Carrosserie', $event->getScore()];
            break;
            case EngineTest::class :
                $params = ['0', 'Moteur', $event->getScore()];
            break;
            case PitStopTest::class :
                $params = ['0', 'Arrêt aux stands', '¤'];
            break;
            case StartTest::class :
                $params = ['0', 'Départ', $event->getScore()];
            break;
            case SuspensionTest::class :
                $params = ['0', 'Tenue de route', $event->getScore()];
            break;
            default :
                $params = ['0', 'Non défini', '?'];
            break;
        }
        return vsprintf($returned, $params);
    }

}
