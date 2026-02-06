<?php

namespace Disjfa\TimetableBundle\EventListener;

use Disjfa\MenuBundle\Menu\ConfigureAdminMenu;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class AdminMenuListener
{
    public function __invoke(ConfigureAdminMenu $event)
    {
        $event->getMenu()->addChild('Timetables', ['route' => 'disjfa_timetable_admin_timetable_index']);
    }
}
