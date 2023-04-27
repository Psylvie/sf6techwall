<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/todo')]

class TodoController extends AbstractController
{
    #[Route('/', name: 'todo')]
    public function index(Request $request): Response
    {
        $session =$request->getSession();
        //affiche tebleau de todo

        //sinon initialise
        if (!$session->has(name: 'todos')){
            $todos =[
                'achat'=>'acheter une clé USB',
                'cours'=>'Finaliser mon cours',
                'correction'=>'corriger mes examens'
            ];
            $session->set('todos', $todos);
            $this->addFlash('info', "la liste des todos viens d'être initialisée");
        }
        // si j'ai mon tableau de todo dans ma session je l'affiche
        return $this->render('todo/index.html.twig');
    }

    #[Route('/add/{name}/{content}',
        //par default: {name?par default}/{content?par default}
        name: 'todo.add',
        defaults: ['content'=>'par default']
    )]
    public function addTodo(Request $request, $name, $content) :RedirectResponse{

        // utilitaion flashmessages (getFlashBag, add) pour la twig app.session.flashbag.get('')
        $session =$request->getSession();
        //verifier si tableau dans session
        if($session->has(name: 'todos')){
            //si oui
                // verif si deja un todo avec meme nom
            $todos = $session->get('todos');
            if (isset($todos[$name])){
                //si oui affiche erreur
                $this->addFlash('error',  "le todo $name existe déjà !");

            }else{
                $todos[$name]= $content;

                //si non ajout et affiche message succes
                $this->addFlash('success',  "le todo $name a été ajouté avec succes ");
                //remet les todos a jours
                $session->set('todos', $todos);

            }


        }else{
            //si non
            // afficher une erreur et redirection vers controller index
            $this->addFlash('error', "la liste des todos n'est pas encore  initialisée");

        }
        return $this->redirectToRoute('todo');



    }

    #[Route('/update/{name}/{content}', name: 'todo.update')]
    public function updateTodo(Request $request, $name, $content):RedirectResponse{

        // utilitaion flashmessages (getFlashBag, add) pour la twig app.session.flashbag.get('')
        $session =$request->getSession();
        //verifier si tableau dans session
        if($session->has(name: 'todos')){
            //si oui
            // verif si deja un todo avec meme nom
            $todos = $session->get('todos');
            if (!isset($todos[$name])){
                //si oui affiche erreur
                $this->addFlash('error',  "le todo $name n'existe pas !");

            }else{
                $todos[$name]= $content;

                //si non ajout et affiche message succes
                $this->addFlash('success',  "le todo $name a été modifié avec succes ");
                //remet les todos a jours
                $session->set('todos', $todos);

            }


        }else{
            //si non
            // afficher une erreur et redirection vers controller index
            $this->addFlash('error', "la liste des todos n'est pas encore  initialisée");

        }
        return $this->redirectToRoute('todo');



    }

    #[Route('/delete/{name}', name: 'todo.delete')]
    public function deleteTodo(Request $request, $name):RedirectResponse{

        // utilitaion flashmessages (getFlashBag, add) pour la twig app.session.flashbag.get('')
        $session =$request->getSession();
        //verifier si tableau dans session
        if($session->has(name: 'todos')){
            //si oui
            // verif si deja un todo avec meme nom
            $todos = $session->get('todos');
            if (!isset($todos[$name])){
                //si oui affiche erreur
                $this->addFlash('error',  "le todo $name n'existe pas !");

            }else{
                unset($todos[$name]);

                //si non supprime et affiche message succes
                $this->addFlash('success',  "le todo $name a été supprimé avec succes ");
                //remet les todos a jours
                $session->set('todos', $todos);

            }


        }else{
            //si non
            // afficher une erreur et redirection vers controller index
            $this->addFlash('error', "la liste des todos n'est pas encore  initialisée");

        }
        return $this->redirectToRoute('todo');



    }

    #[Route('/reset', name: 'todo.reset')]
    public function resetTodo(Request $request):RedirectResponse{


        $session =$request->getSession();
        $session->remove('todos');

        return $this->redirectToRoute('todo');

    }
}
