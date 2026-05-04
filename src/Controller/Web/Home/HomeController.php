<?php

namespace Controller\Web\Home;

use Application\Home\HomePage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HomeController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly HomePage $homePage,
    ) {}

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(): Response
    {
        return new Response(
            $this->twig->render('home/index.html.twig', $this->homePage->toArray())
        );
    }
}