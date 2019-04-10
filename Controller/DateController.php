<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Form\Type\TimetableDateType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/timetable/date")
 */
class DateController extends Controller
{
    /**
     * @Route("/create/{timetable}", name="disjfa_timetable_date_create")
     *
     * @param Request   $request
     * @param Timetable $timetable
     *
     * @return Response
     */
    public function createAction(Request $request, Timetable $timetable)
    {
        $timetableDate = new TimetableDate($timetable);
        $form = $this->createForm(TimetableDateType::class, $timetableDate);

        return $this->handleForm($form, $request);
    }

    /**
     * @Route("/{timetableDate}/edit", name="disjfa_timetable_date_edit")
     *
     * @param Request       $request
     * @param TimetableDate $timetableDate
     *
     * @return Response
     */
    public function editAction(Request $request, TimetableDate $timetableDate)
    {
        $form = $this->createForm(TimetableDateType::class, $timetableDate);

        return $this->handleForm($form, $request);
    }

    /**
     * @param FormInterface $form
     * @param Request       $request
     *
     * @return Response
     */
    private function handleForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TimetableDate $timetableDate */
            $timetableDate = $form->getData();

            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($timetableDate);
            $entitymanager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_date_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetableDate->getTimetable()->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/Timetable/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
