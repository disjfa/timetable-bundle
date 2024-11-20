<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Doctrine\Persistence\ManagerRegistry;
use Spatie\Color\Contrast;
use Spatie\Color\Hex;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     *
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/create', name: 'disjfa_timetable_timetable_create')]
    public function createAction(Request $request)
    {
        $form = $this->createForm(TimetableType::class, new Timetable());

        return $this->handleForm($form, $request);
    }

    /**
     *
     * @param Timetable $timetable
     * @return Response
     */
    #[Route(path: '/{timetable}/show', name: 'disjfa_timetable_timetable_show')]
    public function showAction(Timetable $timetable)
    {
        return $this->render('@DisjfaTimetable/Timetable/show.html.twig', [
            'timetable' => $timetable,
        ]);
    }

    /**
     *
     * @param Request $request
     * @param Timetable $timetable
     * @return Response
     */
    #[Route(path: '/{timetable}/edit', name: 'disjfa_timetable_timetable_edit')]
    public function editAction(Request $request, Timetable $timetable)
    {
        $form = $this->createForm(TimetableType::class, $timetable);

        return $this->handleForm($form, $request);
    }

    /**
     * @param FormInterface $form
     * @param Request $request
     *
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
        ]);
    }
}
