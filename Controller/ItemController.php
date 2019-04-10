<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Entity\TimetableItem;
use Disjfa\TimetableBundle\Entity\TimetablePlace;
use Disjfa\TimetableBundle\Form\Type\TimetableItemType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/timetable/item")
 */
class ItemController extends Controller
{
    /**
     * @Route("/create/{timetablePlace}/{timetableDate}", name="disjfa_timetable_item_create")
     *
     * @param Request        $request
     * @param TimetablePlace $timetablePlace
     * @param TimetableDate  $timetableDate
     *
     * @return Response
     */
    public function createAction(Request $request, TimetablePlace $timetablePlace, TimetableDate $timetableDate)
    {
        $timetableItem = new TimetableItem($timetablePlace, $timetableDate);
        $form = $this->createForm(TimetableItemType::class, $timetableItem);

        return $this->handleForm($form, $request);
    }

    /**
     * @Route("/{timetableItem}/edit", name="disjfa_timetable_item_edit")
     *
     * @param Request       $request
     * @param TimetableItem $timetableItem
     *
     * @return Response
     */
    public function editAction(Request $request, TimetableItem $timetableItem)
    {
        $form = $this->createForm(TimetableItemType::class, $timetableItem);

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
            /** @var TimetableItem $timetableItem */
            $timetableItem = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($timetableItem);
            $entityManager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_item_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetableItem->getPlace()->getTimetable()->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/Timetable/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
