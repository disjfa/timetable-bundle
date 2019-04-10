<?php

namespace Disjfa\TimetableBundle\Controller;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Disjfa\TimetableBundle\Timetable\TimetablePresenter;
use Disjfa\TimetableBundle\Transformer\TimetableTransformer;
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
     * @var TimetableTransformer
     */
    private $timetableTransformer;

    public function __construct(TimetableTransformer $timetableTransformer)
    {
        $this->timetableTransformer = $timetableTransformer;
    }

    /**
     * @Route("", name="disjfa_timetable_timetable_index")
     */
    public function indexAction()
    {
        return $this->render('@DisjfaTimetable/Timetable/index.html.twig', [
            'timetables' => $this->getDoctrine()->getRepository(Timetable::class)->findAll(),
        ]);
    }

    /**
     * @Route("/create", name="disjfa_timetable_timetable_create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $form = $this->createForm(TimetableType::class, new Timetable());

        return $this->handleForm($form, $request);
    }

    /**
     * @Route("/{timetable}/show", name="disjfa_timetable_timetable_show")
     *
     * @param Timetable $timetable
     *
     * @return Response
     */
    public function showAction(Timetable $timetable)
    {
        return $this->render('@DisjfaTimetable/Timetable/show.html.twig', [
            'timetable' => new TimetablePresenter($timetable),
        ]);
    }

    /**
     * @Route("/{timetable}/edit", name="disjfa_timetable_timetable_edit")
     *
     * @param Request   $request
     * @param Timetable $timetable
     *
     * @return Response
     */
    public function editAction(Request $request, Timetable $timetable)
    {
        $form = $this->createForm(TimetableType::class, $timetable);

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
