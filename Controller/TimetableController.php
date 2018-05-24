<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/timetable")
 */
class TimetableController extends Controller
{

    /**
     * @Route("", name="disjfa_timetable_timetable_index")
     */
    public function indexAction()
    {
        return $this->render('@DisjfaTimetable/Timetable/index.html.twig');
    }

    /**
     * @Route("/create", name="disjfa_timetable_timetable_create")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(TimetableType::class);

        return $this->handleForm($form, $request);
    }

    /**
     * @Route("/{timetable}/show", name="disjfa_timetable_timetable_show")
     * @param Timetable $timetable
     * @return Response
     */
    public function showAction(Timetable $timetable)
    {
        $minutes = 60 * 15;
        $items = new ArrayCollection();

        foreach ($timetable->getDates() as $date) {
            $dateStart = null;
            foreach ($date->getItems() as $item) {
                if (null === $dateStart) {
                    $dateStart = $item->getDateStart();
                }

                if ($dateStart > $item->getDateStart()) {
                    $dateStart = $item->getDateStart();
                }

                if ($dateStart > $item->getDateEnd()) {
                    $dateStart = $item->getDateEnd();
                }
            }

            $timeStart = $dateStart->getTimestamp();
            foreach ($date->getItems() as $item) {
                $items->set($item->getId(), [
                    'start' => ($item->getDateStart()->getTimestamp() - $timeStart) / $minutes + 2,
                    'end' => ($item->getDateEnd()->getTimestamp() - $timeStart) / $minutes + 2,
                ]);
            }
        }


        return $this->render('DisjfaTimetable/Timetable/show.html.twig', [
            'timetable' => $timetable,
            'items' => $items,
        ]);
    }

    /**
     * @Route("/{timetable}/edit", name="disjfa_timetable_timetable_edit")
     * @param Request $request
     * @param Timetable $timetable
     * @return Response
     */
    public function editAction(Request $request, Timetable $timetable)
    {
        $form = $this->createForm(TimetableType::class, $timetable);

        return $this->handleForm($form, $request);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     * @return Response
     */
    private function handleForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Timetable $timetable */
            $timetable = $form->getData();

            $entitymanager = $this->getDoctrine()->getManager();
            $entitymanager->persist($timetable);
            $entitymanager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetable->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/Timetable/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
