<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Service\MailerService;
use App\Service\UploaderService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
             'nbre'=>$nbre,
//              'ageBadgeColor'=>$ageBadgeColor,
                ]);

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

    #[Route('/edit/{id?0}', name: 'personne.edit')]
    public function addPersonne(
        Personne $personne = null,
        ManagerRegistry $doctrine,
        Request $request,
        UploaderService $uploaderService,
        MailerService $mailer,
    ): Response
    {

        $new = false;

        $ageBadgeColor = $personne->getAgeBadgeColor();
        if (!$personne){
            $new = true;
            $personne = new Personne();
        }

        // crée moi un formulaire (description objet du formulaire , l'objet )
        //$personne = l'image du formulaire
        $form = $this->createForm(PersonneType::class, $personne);
        $form->remove('createdAt');
        $form->remove('updatedAt');
        // formulaire va aller traiter la requete
        $form->handleRequest($request);
        // formulaire soumis?
        if ($form->isSubmitted() && $form->isValid()){

            // si oui : ajout dans bdd

            // upload image
            $photo = $form->get('photo')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($photo) {
                $directory = $this->getParameter('personne_directory');



                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $personne->setImage($uploaderService->uploadFile($photo, $directory));
            }
            if($new){
                $message = 'a été ajoutée avec succés ';
                $personne->setCreatedBy($this->getUser());
            }else{
                $message = ' a été mis a jour avec succés';
            }
            $entityManager = $doctrine->getManager();
            $entityManager->persist($personne);
            $entityManager->flush();


            $mailMessage = $personne->getFirstname().' '.$personne->getName().' '.$message;


            // message succes
            $this->addFlash('success', 'Votre profil a été enregistré');
            $mailer->sendEmail(content: $mailMessage);


            //redirection vers liste personne
            return $this->redirectToRoute('personne.list');
        }else{

            //si non : affiche formulaire

            return $this->render('personne/add-personne.html.twig', [

                'form'=> $form->createView(),
                'personne' => $personne,
                'ageBadgeColor'=>$ageBadgeColor,

            ]);
        }



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
