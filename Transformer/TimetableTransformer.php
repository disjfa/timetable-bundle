<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Security\TimetableVoter;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TimetableTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [
        'dates',
        'places',
        'items',
    ];

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationCheker;
    /**
     * @var TimetableDateTransformer
     */
    private $timetableDateTransformer;
    /**
     * @var TimetablePlaceTransformer
     */
    private $timetablePlaceTransformer;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var CsrfTokenManagerInterface
     */
    private $tokenManager;
    /**
     * @var TimetableItemTransformer
     */
    private $timetableItemTransformer;

    /**
     * TimetableTransformer constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationCheker
     * @param RouterInterface               $router
     * @param TimetableDateTransformer      $timetableDateTransformer
     * @param TimetablePlaceTransformer     $timetablePlaceTransformer
     * @param TimetableItemTransformer      $timetableItemTransformer
     * @param CsrfTokenManagerInterface     $tokenManager
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationCheker,
        RouterInterface $router,
        TimetableDateTransformer $timetableDateTransformer,
        TimetablePlaceTransformer $timetablePlaceTransformer,
        TimetableItemTransformer $timetableItemTransformer,
        CsrfTokenManagerInterface $tokenManager
    ) {
        $this->authorizationCheker = $authorizationCheker;
        $this->router = $router;
        $this->timetableDateTransformer = $timetableDateTransformer;
        $this->timetablePlaceTransformer = $timetablePlaceTransformer;
        $this->timetableItemTransformer = $timetableItemTransformer;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param Timetable $timetable
     *
     * @return array
     */
    public function transform(Timetable $timetable)
    {
        $data = [
            'id' => $timetable->getId(),
            'title' => $timetable->getTitle(),
            'side' => $timetable->getSide(),
            'token' => $this->tokenManager->getToken('timetable')->getValue(),
        ];

        $links = [];
        if ($this->authorizationCheker->isGranted(TimetableVoter::VIEW, $timetable)) {
            $links['get_url'] = $this->router->generate('disjfa_timetable_api_timetable_show', [
                'timetable' => $timetable->getId(),
            ]);
        }
        if ($this->authorizationCheker->isGranted(TimetableVoter::PATCH, $timetable)) {
            $links['patch_url'] = $this->router->generate('disjfa_timetable_api_timetable_patch', [
                'timetable' => $timetable->getId(),
            ]);
        }

        $data['links'] = $links;

        return $data;
    }

    /**
     * @param Timetable $timetable
     *
     * @return Collection
     */
    public function includeDates(Timetable $timetable)
    {
        $dates = $timetable->getDates();

        return $this->collection($dates, $this->timetableDateTransformer);
    }

    /**
     * @param Timetable $timetable
     *
     * @return Collection
     */
    public function includePlaces(Timetable $timetable)
    {
        $places = $timetable->getPlaces();

        return $this->collection($places, $this->timetablePlaceTransformer);
    }

    /**
     * @param Timetable $timetable
     *
     * @return Collection
     */
    public function includeItems(Timetable $timetable)
    {
        $dates = $timetable->getDates();

        $items = [];
        foreach ($dates as $date) {
            $items = array_merge($items, $date->getItems()->toArray());
        }

        return $this->collection($items, $this->timetableItemTransformer);
    }
}
