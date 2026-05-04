<?php

namespace Controller\Web\Trail;

use Application\Trail\TrailCataloguePage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Trailmind\Trail\TrailRepository;
use Twig\Environment;

class TrailCatalogueController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly TrailRepository $trailRepository,
        private readonly TrailCataloguePage $trailCataloguePage,
    ) {}

    #[Route('/trails', methods: ['GET'], name: 'trail_collection')]
    public function index(): Response
    {
        return new Response(
            $this->twig->render(
                'trails/index.html.twig',
                $this->trailCataloguePage->toArray($this->trailRepository->findAll())
            )
        );
    }
}