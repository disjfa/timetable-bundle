<?php

namespace Disjfa\TimetableBundle\Transformer;

use Disjfa\TimetableBundle\Entity\Timetable;
use Disjfa\TimetableBundle\Security\TimetableVoter;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class TimetableTransformer extends TransformerAbstract
{
    /**
     * @var array
     */
    protected $defaultIncludes = [
        'dates',
        'places'
    ];

    /**
     * @var AuthorizationCheckerInterface
     */
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
    /**
     * @var CsrfTokenManagerInterface
     */
    private $tokenManager;

    /**
     * TimetableTransformer constructor.
     * @param AuthorizationCheckerInterface $authorizationCheker
     * @param RouterInterface $router
     * @param TimetableDatesTransformer $timetableDatesTransformer
     * @param TimetablePlacesTransformer $timetablePlacesTransformer
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationCheker,
        RouterInterface $router,
        TimetableDatesTransformer $timetableDatesTransformer,
        TimetablePlacesTransformer $timetablePlacesTransformer,
        CsrfTokenManagerInterface $tokenManager
    )
    {
        $this->authorizationCheker = $authorizationCheker;
        $this->timetableDatesTransformer = $timetableDatesTransformer;
        $this->timetablePlacesTransformer = $timetablePlacesTransformer;
        $this->router = $router;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param Timetable $timetable
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
            $links['get_url'] = $this->router->generate('disjfa_timetable_api_timetable_get', [
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
     * @return Collection
     */
    public function includeDates(Timetable $timetable)
    {
        $dates = $timetable->getDates();

        return $this->collection($dates, $this->timetableDatesTransformer);
    }

    /**
     * @param Timetable $timetable
     * @return Collection
     */
    public function includePlaces(Timetable $timetable)
    {
        $places = $timetable->getPlaces();

        return $this->collection($places, $this->timetablePlacesTransformer);
    }


}