<?php

namespace Disjfa\TimetableBundle\Controller;

use App\Entity\Settings;
use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Entity\TimetableDate;
use Disjfa\TimetableBundle\Entity\TimetableItem;
use Disjfa\TimetableBundle\Entity\TimetablePlace;
use Disjfa\TimetableBundle\Form\Type\TimetableSetupType;
use Disjfa\TimetableBundle\Form\Type\TimetableType;
use Disjfa\TimetableBundle\Security\TimetableVoter;
use Disjfa\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Spatie\Color\Hex;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path: '/timetable')]
class TimetableController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route(path: '', name: 'disjfa_timetable_timetable_index')]
    public function indexAction(#[CurrentUser] ?UserInterface $user = null)
    {
        $timetables = [];
        $demoTimetables = [];
        if ($user) {
            $timetables = $this->entityManager->getRepository(Timetable::class)->findAllByOptions(user: $user);
        }

        $demoUser = $this->entityManager->getRepository(Settings::class)->findOneBy(['type' => 'demo_user']);
        if ($demoUser->getValue()) {
            $user = $this->entityManager->getRepository(User::class)->find($demoUser->getValue());
            if ($user) {
                $demoTimetables = $this->entityManager->getRepository(Timetable::class)->findAllByOptions(user: $user);
            }
        }

        return $this->render('@DisjfaTimetable/timetable/index.html.twig', [
            'timetables' => $timetables,
            'demoTimetables' => $demoTimetables,
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

        return $this->handleForm($form, $request, true);
    }

    /**
     * @return Response
     */
    #[Route(path: '/{timetable}', name: 'disjfa_timetable_timetable_show')]
    public function showAction(Timetable $timetable)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::VIEW, $timetable);

        return $this->render('@DisjfaTimetable/timetable/show.html.twig', [
            'timetable' => $timetable,
        ]);
    }

    #[Route(path: '/{timetable}/manifest.webmanifest', name: 'disjfa_timetable_timetable_manifest')]
    public function manifestAction(Timetable $timetable): Response
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        try {
            $manifest = new File($projectDir.'/public/favicons/manifest.webmanifest');
            $json = json_decode($manifest->getContent(), true);
        } catch (FileNotFoundException $e) {
            $json = [];
        }

        $json['name'] = $timetable->getTitle();
        $json['short_name'] = $timetable->getTitle();
        $json['description'] = $timetable->getIntro();
        $json['start_url'] = $this->generateUrl('disjfa_timetable_timetable_show', ['timetable' => $timetable->getId(), 'homescreen' => 1]);
        $json['theme_color'] = $timetable->getHeaderBg();
        $json['background_color'] = $timetable->getBodyBg();

        return new JsonResponse($json);
    }

    #[Route(path: '/{timetable}/about', name: 'disjfa_timetable_timetable_about')]
    public function about(Timetable $timetable)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::VIEW, $timetable);

        return $this->render('@DisjfaTimetable/timetable/about.html.twig', [
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

    #[Route(path: '/{timetable}/setup', name: 'disjfa_timetable_timetable_setup')]
    public function setupAction(Request $request, Timetable $timetable)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::UPDATE, $timetable);

        if (0 === $timetable->getDates()->count()) {
            $timetable->setDates(new ArrayCollection([new TimetableDate()]));
        }
        if (0 === $timetable->getPlaces()->count()) {
            $timetable->setPlaces(new ArrayCollection([new TimetablePlace()]));
        }

        $form = $this->createForm(TimetableSetupType::class, $timetable);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dates = $timetable->getDates();
            foreach ($dates as $date) {
                $date->setTimetable($timetable);
            }
            $places = $timetable->getPlaces();
            foreach ($places as $place) {
                $place->setTimetable($timetable);
            }
            $this->entityManager->persist($timetable);
            $this->entityManager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_saved');

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetable->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/timetable/setup.html.twig', [
            'form' => $form,
            'timetable' => $timetable,
        ]);
    }

    #[Route(path: '/{timetable}/{date}/{place}', name: 'disjfa_timetable_timetable_date_and_place')]
    public function dateAndPlaceAction(Request $request, Timetable $timetable, TimetableDate $date, TimetablePlace $place)
    {
        $this->denyAccessUnlessGranted(TimetableVoter::VIEW, $timetable);

        if (false === $timetable->getDates()->contains($date)) {
            throw $this->createAccessDeniedException('Date does not compute');
        }
        if (false === $timetable->getPlaces()->contains($place)) {
            throw $this->createAccessDeniedException('Date does not compute');
        }

        $currentDate = new \DateTime($request->query->get('date', 'now'));
        $setPrevious = true;

        $previous = null;
        $current = null;
        $nextItems = new ArrayCollection();
        foreach ($date->getItems() as $item) {
            /** @var TimetableItem $item */
            if ($item->getDateEnd() > $currentDate) {
                $setPrevious = false;
            }

            if ($setPrevious) {
                $previous = $item;
            }

            if ($item->getDateStart() > $currentDate) {
                $nextItems->add($item);
            }

            if ($item->getDateStart() < $currentDate && $item->getDateEnd() > $currentDate) {
                $current = $item;
            }
        }

        return $this->render('@DisjfaTimetable/timetable/sheet.html.twig', [
            'timetable' => $timetable,
            'date' => $date,
            'place' => $place,
            'currentDate' => $currentDate,
            'previous' => $previous,
            'current' => $current,
            'nextItems' => $nextItems,
        ]);
    }

    /**
     * @return Response
     */
    private function handleForm(FormInterface $form, Request $request, bool $toSetup = false)
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Timetable $timetable */
            $timetable = $form->getData();

            $this->entityManager->persist($timetable);
            $this->entityManager->flush();

            $this->addFlash('success', 'timetable.flash.timetable_saved');

            if ($toSetup) {
                return $this->redirectToRoute('disjfa_timetable_timetable_setup', [
                    'timetable' => $timetable->getId(),
                ]);
            }

            return $this->redirectToRoute('disjfa_timetable_timetable_show', [
                'timetable' => $timetable->getId(),
            ]);
        }

        return $this->render('@DisjfaTimetable/timetable/form.html.twig', [
            'form' => $form->createView(),
            'timetable' => $form->getData(),
        ]);
    }
}
