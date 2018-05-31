<?php

namespace Disjfa\TimetableBundle\Controller\Api;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Disjfa\TimetableBundle\Timetable\TimetablePresenter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/timetable")
 */
class TimetableController extends Controller
{
    /**
     * @Route("/{timetable}/show", name="disjfa_timetable_api_timetable_show")
     * @Method("GET")
     * @param Timetable $timetable
     * @return Response
     */
    public function showAction(Timetable $timetable)
    {
        return new JsonResponse(new TimetablePresenter($timetable));
    }

    /**
     * @Route("/{timetable}", name="disjfa_timetable_api_timetable_get")
     * @Method("GET")
     * @param Timetable $timetable
     * @return Response
     */
    public function getAction(Timetable $timetable)
    {
        $form = $this->createForm(TimetableType::class, $timetable);
        $tokenManager = $this->get('security.csrf.token_manager');

        return new JsonResponse([
            'timetable' => new TimetablePresenter($timetable, false),
            'token' => $tokenManager->getToken($form->getName()),
        ]);
    }
}
