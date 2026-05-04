<?php

namespace spec\Controller\Web\Home;

use Application\Home\HomePage;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class HomeControllerSpec extends ObjectBehavior
{
    function let(Environment $twig, HomePage $homePage)
    {
        $this->beConstructedWith($twig, $homePage);
    }

    function it_renders_the_home_page(Environment $twig, HomePage $homePage)
    {
        $pageData = [
            'navigation' => [
                ['label' => 'Highlights', 'href' => '#highlights'],
            ],
        ];

        $homePage->toArray()->willReturn($pageData);
        $twig->render('home/index.html.twig', $pageData)->willReturn('<html>homepage</html>');

        $this->index()->shouldBeLike(new Response('<html>homepage</html>'));
    }
}