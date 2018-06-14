<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Entity\TimetablePlace;
use Disjfa\TimetableBundle\Form\Type\TimetablePlaceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/timetable/place")
 */
class PlaceController extends Controller
{
    /**
     * @Route("/create/{timetable}", name="disjfa_timetable_place_create")
     */
    public function createAction(Request $request, Timetable $timetable)
    {
        $timetableDate = new TimetablePlace($timetable);
        $form = $this->createForm(TimetablePlaceType::class, $timetableDate);

        return $this->handleForm($form, $request);
    }

    /**
     * @Route("/{timetablePlace}/edit", name="disjfa_timetable_place_edit")
     */
    public function editAction(Request $request, TimetablePlace $timetablePlace)
    {
        $form = $this->createForm(TimetablePlaceType::class, $timetablePlace);

        return $this->handleForm($form, $request);
    }

    private function handleForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TimetableDate $timetableDate */
            $timetableDate = $form->getData();

            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($timetableDate);
            $entitymanager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_place_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetableDate->getTimetable()->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/Timetable/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
