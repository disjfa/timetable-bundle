<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Disjfa\TimetableBundle\Security\TimetableVoter;
use Doctrine\Persistence\ManagerRegistry;
use Spatie\Color\Hex;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/timetable')]
class TimetableController extends AbstractController
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    #[Route(path: '', name: 'disjfa_timetable_timetable_index')]
    public function indexAction()
    {
        return $this->render('@DisjfaTimetable/Timetable/index.html.twig', [
            'timetables' => $this->registry->getRepository(Timetable::class)->findAll(),
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: '/create', name: 'disjfa_timetable_timetable_create')]
    public function createAction(Request $request, #[CurrentUser] ?UserInterface $user)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $timeTable = new Timetable();
        $timeTable->addUser($user);
        $timeTable->setBodyBg(Hex::fromString('#ecf0f1')->toHex());
        $timeTable->setHeaderBg(Hex::fromString('#16a085')->toHex());
        $timeTable->setBoxBg(Hex::fromString('#3498db')->toHex());

        $form = $this->createForm(TimetableType::class, $timeTable);

        return $this->handleForm($form, $request);
    }

    /**
     * @return Response
     */
    #[Route(path: '/{timetable}/show', name: 'disjfa_timetable_timetable_show')]
    public function showAction(Timetable $timetable)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::VIEW, $timetable);

        return $this->render('@DisjfaTimetable/Timetable/show.html.twig', [
            'timetable' => $timetable,
        ]);
    }

    #[Route(path: '/{timetable}/about', name: 'disjfa_timetable_timetable_about')]
    public function about(Timetable $timetable)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::VIEW, $timetable);

        return $this->render('@DisjfaTimetable/Timetable/about.html.twig', [
            'timetable' => $timetable,
        ]);
    }

    /**
     * @return Response
     */
    #[Route(path: '/{timetable}/edit', name: 'disjfa_timetable_timetable_edit')]
    public function editAction(Request $request, Timetable $timetable)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetable);

        $form = $this->createForm(TimetableType::class, $timetable);

        return $this->handleForm($form, $request);
    }

    /**
     * @return Response
     */
    private function handleForm(FormInterface $form, Request $request)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Timetable $timetable */
            $timetable = $form->getData();

            $entitymanager = $this->registry->getManager();
            $entitymanager->persist($timetable);
            $entitymanager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetable->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/Timetable/form.html.twig', [
            'form' => $form->createView(),
            'timetable' => $form->getData(),
        ]);
    }
}
