<?php

namespace Disjfa\TimetableBundle\Controller\Api;

use App\Form\Errors\Serializer;
use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Disjfa\TimetableBundle\Transformer\TimetableTransformer;
use Doctrine\ORM\EntityManagerInterface;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use PhpCsFixer\Console\Report\FixReport\ReporterFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/timetable')]
class TimetableController extends AbstractController
{
    private TimetableTransformer $timetableTransformer;
    private EntityManagerInterfac $entityManager;

    public function __construct(TimetableTransformer $timetableTransformer, EntityManagerInterface $entityManager)
    {
        $this->timetableTransformer = $timetableTransformer;
        $this->entityManager = $entityManager;
    }

    #[Route('/{timetable}/show', name: 'disjfa_timetable_api_timetable_show', methods: ['GET'])]
    public function showAction(Timetable $timetable): Response
    {
        $item = new Item($timetable, $this->timetableTransformer);
        $manager = new Manager();

        return new JsonResponse($manager->createData($item)->toArray());
    }

    #[Route('/{timetable}', name: 'disjfa_timetable_api_timetable_patch', methods: ['PATCH'])]
    public function patchAction(Timetable $timetable, Request $request): Response
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
