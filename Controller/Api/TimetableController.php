<?php

namespace Disjfa\TimetableBundle\Controller\Api;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Disjfa\TimetableBundle\Timetable\TimetablePresenter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/timetable")
 */
class TimetableController extends Controller
{
    /**
     * @Route("/{timetable}/show", name="disjfa_timetable_api_timetable_show")
     * @param Timetable $timetable
     * @return Response
     */
    public function showAction(Timetable $timetable)
    {
        return new JsonResponse(new TimetablePresenter($timetable));
    }

}
