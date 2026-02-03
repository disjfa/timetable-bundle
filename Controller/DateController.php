<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Form\Type\TimetableDateType;
use Disjfa\TimetableBundle\Security\TimetableVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/timetable/date')]
class DateController extends AbstractController
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route('/create/{timetable}', name: 'disjfa_timetable_date_create', methods: ['GET', 'POST'])]
    public function createAction(Request $request, Timetable $timetable): Response
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetable);

        $timetableDate = new TimetableDate($timetable);
        $form = $this->createForm(TimetableDateType::class, $timetableDate);

        return $this->handleForm($form, $request);
    }

    #[Route('/{timetableDate}/edit', name: 'disjfa_timetable_date_edit', methods: ['GET', 'POST'])]
    public function editAction(Request $request, TimetableDate $timetableDate): Response
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetableDate->getTimetable());

        $form = $this->createForm(TimetableDateType::class, $timetableDate);

        return $this->handleForm($form, $request);
    }

    private function handleForm(FormInterface $form, Request $request): Response
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TimetableDate $timetableDate */
            $timetableDate = $form->getData();

            $this->entityManager->persist($timetableDate);
            $this->entityManager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_date_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetableDate->getTimetable()->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/Timetable/form.html.twig', [
            'form' => $form->createView(),
            'timetable' => $form->getData()->getTimetable(),
        ]);
    }
}
