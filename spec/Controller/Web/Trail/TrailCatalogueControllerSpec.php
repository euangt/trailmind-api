<?php

namespace spec\Controller\Web\Trail;

use Application\Trail\TrailCataloguePage;
use Controller\Trail\TrailCatalogueController;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;
use Trailmind\Trail\Trail;
use Trailmind\Trail\TrailRepository;
use Twig\Environment;

class TrailCatalogueControllerSpec extends ObjectBehavior
{
    function let(Environment $twig, TrailRepository $trailRepository, TrailCataloguePage $trailCataloguePage)
    {
        $this->beConstructedWith($twig, $trailRepository, $trailCataloguePage);
    }

    function it_renders_the_trail_catalogue(
        Environment $twig,
        TrailRepository $trailRepository,
        TrailCataloguePage $trailCataloguePage,
        Trail $trail
    ) {
        $trails = [$trail];
        $pageData = [
            'navigation' => [],
            'heroHighlights' => [],
            'summaryEntries' => [],
            'trailCards' => [],
            'trailCount' => 1,
            'outlookNotes' => [],
        ];

        $trailRepository->findAll()->willReturn($trails);
        $trailCataloguePage->toArray($trails)->willReturn($pageData);
        $twig->render('trails/index.html.twig', $pageData)->willReturn('<html>trail catalogue</html>');

        $this->index()->shouldBeLike(new Response('<html>trail catalogue</html>'));
    }
}