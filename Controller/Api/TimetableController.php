<?php

namespace Disjfa\TimetableBundle\Controller\Api;

use App\Form\Errors\Serializer;
use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Disjfa\TimetableBundle\Transformer\TimetableTransformer;
use Doctrine\ORM\EntityManagerInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api/timetable")
 */
class TimetableController extends Controller
{
    /**
     * @var TimetableTransformer
     */
    private $timetableTransformer;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TimetableController constructor.
     *
     * @param TimetableTransformer   $timetableTransformer
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(TimetableTransformer $timetableTransformer, EntityManagerInterface $entityManager)
    {
        $this->timetableTransformer = $timetableTransformer;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/{timetable}/show", name="disjfa_timetable_api_timetable_show")
     * @Method("GET")
     *
     * @param Timetable $timetable
     *
     * @return Response
     */
    public function showAction(Timetable $timetable)
    {
        $item = new Item($timetable, $this->timetableTransformer);
        $manager = new Manager();

        return new JsonResponse($manager->createData($item)->toArray());
    }

    /**
     * @Route("/{timetable}", name="disjfa_timetable_api_timetable_patch")
     * @Method("PATCH")
     *
     * @param Timetable $timetable
     *
     * @return Response
     */
    public function patchAction(Timetable $timetable, Request $request)
    {
        $form = $this->createForm(TimetableType::class, $timetable);
        $data = json_decode($request->getContent(), true);
        $form->submit($data['timetable']);

        if (false === $form->isSubmitted()) {
            return new JsonResponse(null, 400);
        }

        if ($form->isValid()) {
            $this->entityManager->persist($timetable);
            $this->entityManager->flush();

            $item = new Item($timetable, $this->timetableTransformer);
            $manager = new Manager();

            return new JsonResponse($manager->createData($item)->toArray());
        }

        return new JsonResponse([
            'errors' => Serializer::serialize($form),
        ], 422);
    }
}
