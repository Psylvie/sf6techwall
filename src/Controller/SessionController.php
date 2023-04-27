<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SessionController extends AbstractController
{
    #[Route('/session', name: 'session')]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        if ($session->has(name: 'nbVisite')){
            $nbrVisite = $session->get(name: 'nbVisite') +1;
            //met a jour la nouvelle valeur

        } else {
            $nbrVisite = 1;
        }
        $session->set('nbVisite', $nbrVisite);
        return $this->render('session/index.html.twig');
    }
}
