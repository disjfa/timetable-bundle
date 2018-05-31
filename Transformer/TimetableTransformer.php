<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\Timetable;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class TimetableTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'dates',
        'places'
    ];

    private $authorizationCheker;
    /**
     * @var TimetableDatesTransformer
     */
    private $timetableDatesTransformer;
    /**
     * @var TimetablePlacesTransformer
     */
    private $timetablePlacesTransformer;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        AuthorizationCheckerInterface $authorizationCheker,
        RouterInterface $router,
        TimetableDatesTransformer $timetableDatesTransformer,
        TimetablePlacesTransformer $timetablePlacesTransformer
    )
    {
        $this->authorizationCheker = $authorizationCheker;
        $this->timetableDatesTransformer = $timetableDatesTransformer;
        $this->timetablePlacesTransformer = $timetablePlacesTransformer;
        $this->router = $router;
    }

    public function transform(Timetable $timetable)
    {
        $data = [
            'id' => $timetable->getId(),
            'title' => $timetable->getTitle(),
            'side' => $timetable->getSide(),
        ];

        if ($this->authorizationCheker->isGranted('krakaka', $timetable)) {
            $data['email'] = $user->email();
        }
        return $data;
    }

    public function includeDates(Timetable $timetable)
    {
        $dates = $timetable->getDates();

        return $this->collection($dates, $this->timetableDatesTransformer);
    }

    public function includePlaces(Timetable $timetable)
    {
        $places = $timetable->getPlaces();

        return $this->collection($places, $this->timetablePlacesTransformer);
    }


}