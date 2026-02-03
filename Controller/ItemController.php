<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Entity\TimetableItem;
use Disjfa\TimetableBundle\Entity\TimetablePlace;
use Disjfa\TimetableBundle\Form\Type\TimetableItemType;
use Disjfa\TimetableBundle\Security\TimetableVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/timetable/item')]
class ItemController extends AbstractController
{
    public function __construct(protected readonly EntityManagerInterface $entityManager)
    {
    }

    #[Route(path: '/create/{timetablePlace}/{timetableDate}', name: 'disjfa_timetable_item_create')]
    public function createAction(Request $request, TimetablePlace $timetablePlace, TimetableDate $timetableDate)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetablePlace->getTimetable());
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetableDate->getTimetable());

        $timetableItem = new TimetableItem($timetablePlace, $timetableDate);
        $form = $this->createForm(TimetableItemType::class, $timetableItem);

        return $this->handleForm($form, $request);
    }

    #[Route(path: '/{timetableItem}/edit', name: 'disjfa_timetable_item_edit')]
    public function editAction(Request $request, TimetableItem $timetableItem)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetableItem->getDate()->getTimetable());

        $form = $this->createForm(TimetableItemType::class, $timetableItem);

        return $this->handleForm($form, $request);
    }

    private function handleForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var TimetableItem $timetableItem */
            $timetableItem = $form->getData();

            $this->entityManager->persist($timetableItem);
            $this->entityManager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_item_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetableItem->getPlace()->getTimetable()->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/Timetable/form.html.twig', [
            'form' => $form->createView(),
            'timetable' => $form->getData()->getPlace()->getTimetable(),
        ]);
    }
}
