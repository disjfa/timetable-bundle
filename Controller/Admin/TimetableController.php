<?php

declare(strict_types=1);

namespace Disjfa\TimetableBundle\Controller\Admin;

use Disjfa\TimetableBundle\Repository\TimetableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TimetableController extends AbstractController
{
    #[Route('/admin/timetable', name: 'disjfa_timetable_admin_timetable_index')]
    public function index(TimetableRepository $timetableRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $timetables = $timetableRepository->findAll();

        return $this->render('@DisjfaTimetable/admin/timetable/index.html.twig', [
            'timetables' => $timetables,
        ]);
    }
}
