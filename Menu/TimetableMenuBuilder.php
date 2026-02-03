<?php

namespace Disjfa\TimetableBundle\Menu;

use Disjfa\MenuBundle\Menu\ConfigureMenuEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class TimetableMenuBuilder
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * HomeMenuListener constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onMenuConfigure(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        $menu->addChild('timetable', [
            'route' => 'disjfa_timetable_timetable_index',
            'label' => $this->translator->trans('menu.timetable', [], 'site'),
        ])->setExtra('icon', 'fa-calendar');
    }
}
