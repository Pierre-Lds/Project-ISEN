<?php

namespace App\Controller\Unlogged;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UnloggedController extends AbstractController {
    // General
    /**
     * @return Response
     * @Route("/",name="app.homepage")
     */
    public function homepage () : Response {
        return $this->render('Unlogged/homepage.html.twig');
    }
    /**
     * @return Response
     * @Route("/documentation",name="app.documentation")
     */
    public function documentation () : Response {
        return $this->render('Unlogged/documentation.html.twig');
    }
    /**
     * @return Response
     * @Route("/versions",name="app.versions")
     */
    public function versions () : Response {
        return $this->render('Unlogged/versions.html.twig');
    }
}
