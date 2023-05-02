<?php

namespace App\Controller;

use App\Entity\Personne;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('personne')]
class PersonneController extends AbstractController
{
    #[Route('/', name: 'personne.list')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
       $personnes = $repository->findAll();
       return $this->render('personne/index.html.twig',
       ['personnes'=>$personnes]);

    }

    #[Route('/alls/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function personnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personnes = $repository->findPersonnesByAgeInterval($ageMin,$ageMax);
        return $this->render('personne/index.html.twig',
            ['personnes'=>$personnes]);

    }

    #[Route('/stats/age/{ageMin}/{ageMax}', name: 'personne.list.age')]
    public function StatsPersonnesByAge(ManagerRegistry $doctrine, $ageMin, $ageMax): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $stats = $repository->statsPersonnesByAgeInterval($ageMin,$ageMax);
        return $this->render('personne/stats.html.twig',
                ['stats'=>$stats[0],
                'ageMin'=>$ageMin,
                'ageMax'=>$ageMax]);

    }
    #[Route('/alls/{page?1}/{nbre?12}', name: 'personne.list.alls')]
    public function indexAlls(ManagerRegistry $doctrine, $page, $nbre): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
//        findy:
       $personnes = $repository->findBy([], [], $nbre, ($page -1) * $nbre);

       $nbPersonne = $repository->count([]);
//       24
        $nbrePage = ceil($nbPersonne/$nbre);

        return $this->render('personne/index.html.twig',
            ['personnes'=>$personnes,
             'isPaginated'=>true,
             'nbrePage'=> $nbrePage,
             'page'=>$page,
             'nbre'=>$nbre]);

    }

    #[Route('/{id<\d+>}', name: 'personne.detail')]
    public function detail(ManagerRegistry $doctrine, $id): Response
    {
        $repository = $doctrine->getRepository(Personne::class);
        $personne = $repository->find($id);
        if (!$personne){
            $this->addFlash('error', "la personne avec l'id $id n'existe pas");
            return $this->redirectToRoute('personne.list');
        }
        return $this->render('personne/detail.html.twig',
            ['personne'=>$personne]);

    }

    // meme resultat avec version param converter :

//    #[Route('/{id<\d+>}', name: 'personne.detail')]
//    public function detail(Personne $personne  null): Response
//    {
//        if (!$personne){
//            $this->addFlash('error', "la personne n'existe pas");
//            return $this->redirectToRoute('personne.list');
//        }
//        return $this->render('personne/detail.html.twig',
//            ['personne'=>$personne]);
//
//    }

    #[Route('/add', name: 'personne.add')]
    public function addPersonne(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();


        $personne = new Personne();

        $personne->setFirstname('vivie');
        $personne->setName('bobo');
        $personne->setAge('12');


        // ajout operation insertion
        $entityManager->persist($personne);
//        $entityManager->persist($personne2);

        //execute
        $entityManager->flush();

        return $this->render('personne/detail.html.twig', [
            'personne'=>$personne,
        ]);
    }

    #[Route('/delete/{id}', name: 'personne.delete')]
    public function deletePersonne( Personne $personne = null, ManagerRegistry $doctrine): RedirectResponse
    {
//recuperer la personne

        if ($personne){
            $manager= $doctrine->getManager();
// ajoute fonction de suppression dans la transaction

            $manager->remove($personne);
// execute la suppression
            $manager->flush();
            $this->addFlash('success', 'La personne est supprimée ');

        }else{
            $this->addFlash('errror', 'personne inexistante');

        }
       return $this->redirectToRoute('personne.list.alls');

//si personne existe => supprime et retourne flash message

//        sinon flash message erreur


    }

    #[Route('/update/{id}/{name}/{firstname}/{age}', name: 'personne.update')]
    public function updatePersonne( Personne $personne = null, ManagerRegistry $doctrine, $name, $firstname, $age, ): RedirectResponse
    {
//verifie si personne existe

        if ($personne){
           $personne->setName($name);
            $personne->setFirstname($firstname);
            $personne->setAge($age);
            $manager = $doctrine->getManager();
            $manager->persist($personne);
            $manager->flush();
            $this->addFlash('success', 'La personne est mise a jour ');


//sinon flash message erreur et redirection
        }else{
            $this->addFlash('errror', 'personne inexistante');

        }
        return $this->redirectToRoute('personne.list.alls');





    }

}
