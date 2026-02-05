<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Entity\TimetablePlace;
use Disjfa\TimetableBundle\Form\Type\TimetablePlaceType;
use Disjfa\TimetableBundle\Security\TimetableVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/timetable/place')]
class PlaceController extends AbstractController
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/create/{timetable}', name: 'disjfa_timetable_place_create')]
    public function createAction(Request $request, Timetable $timetable)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetable);

        $timetableDate = new TimetablePlace($timetable);
        $form = $this->createForm(TimetablePlaceType::class, $timetableDate);

        return $this->handleForm($form, $request);
    }

    #[Route(path: '/{timetablePlace}/edit', name: 'disjfa_timetable_place_edit')]
    public function editAction(Request $request, TimetablePlace $timetablePlace)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetablePlace->getTimetable());

        $form = $this->createForm(TimetablePlaceType::class, $timetablePlace);

        return $this->handleForm($form, $request);
    }

    private function handleForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TimetableDate $timetableDate */
            $timetableDate = $form->getData();

            $this->entityManager->persist($timetableDate);
            $this->entityManager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_place_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetableDate->getTimetable()->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/place/form.html.twig', [
            'form' => $form->createView(),
            'timetable' => $form->getData()->getTimetable(),
        ]);
    }
}
