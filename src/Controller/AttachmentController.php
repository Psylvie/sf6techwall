<?php

namespace App\Controller;

use App\Entity\EntityAttachment;
use App\Form\EntityAttachmentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AttachmentController extends AbstractController
{
    #[Route('/upload', name: 'upload_attachment')]
    public function upload(Request $request, EntityManagerInterface $entityManager): Response
    {
        $attachment = new EntityAttachment();

        $form = $this->createForm(EntityAttachmentType::class, $attachment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){

         $entityManager->persist($attachment);
         $entityManager->flush();
            $this->addFlash('success', 'Pièce jointe téléchargée avec succès.');
            return $this->redirectToRoute('attachment_list');
        }
        return $this->render('attachment/upload.html.twig', [
            'form'=>$form->createView(),
        ]);
    }
    #[Route('/attachments', name: 'attachment_list')]
    public function listAttachment(EntityManagerInterface $entityManager){

        $attachmentRepository = $entityManager->getRepository(EntityAttachment::class);
        $attachments = $attachmentRepository->findAll();

        return $this->render('attachment/list.html.twig', [
            'attachments' => $attachments,
        ]);
    }

}
