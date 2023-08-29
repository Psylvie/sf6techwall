<?php

namespace App\Controller;

use App\Entity\EntityAttachment;
use App\Form\EntityAttachmentType;
use App\Repository\EntityAttachmentRepository;
use App\Service\UploaderService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;



class AttachmentController extends AbstractController
{
    private $uploaderService;
    public function __construct(UploaderService $uploaderService)
    {
        $this->uploaderService = $uploaderService;
    }

    #[Route('/upload', name: 'upload_attachment')]
    public function upload(Request $request, EntityManagerInterface $entityManager, UploaderService $uploaderService): Response
    {
        $attachment = new EntityAttachment();

        $form = $this->createForm(EntityAttachmentType::class, $attachment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du téléchargement du fichier
            $file = $form->get('filepath')->getData(); // Récupérez le fichier du formulaire

            if ($file instanceof UploadedFile) { // Vérifiez que c'est un UploadedFile
                $directory = $this->getParameter('attachment_directory');
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($directory, $fileName);
                $attachment->setFilepath($fileName);
            }

            // Enregistrement en base de données
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
    #[Route('/attachment/delete/{id}', name: 'attachment.delete', methods: ['GET', 'DELETE'])]
    public function deleteAttachment(
        EntityAttachment $attachment,
        EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($attachment);
        $entityManager->flush();

        return $this->redirectToRoute('attachment_list');
    }
    #[Route('/attachments/download/{id}', name: 'attachment.download')]
    #[ParamConverter('attachment', class: EntityAttachment::class, options: ['id' => 'id'])]
    public function downloadAttachment(EntityAttachment $attachment): Response
    {
        $fileName = $attachment->getFilepath(); // Récupérez le nom du fichier
        $filePath = $this->getParameter('attachment_directory').'/'.$fileName;

        if (file_exists($filePath)) {
            $response = new BinaryFileResponse($filePath);
            $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                $fileName
            );

            return $response;
        }

        return $this->json(['error' => 'Le fichier n\'existe pas.'], 404);
    }

//    http://127.0.0.1:8000/attachments/by-entity/8
    #[Route('/attachments/by-entity/{entityId}', name: 'attachment.by.entity')]
    public function getAttachmentByEntity(?int $entityId,EntityAttachmentRepository $attachmentRepository): Response
    {
        $attachments = $attachmentRepository->findByEntityId($entityId);

        return $this->render('attachment/by_entity.html.twig', [
            'attachments' => $attachments,
        ]);
    }
    #[Route('/attachments/by-type/{type}', name: 'attachment.by_type')]
    public function getAttachmentByType(string $type, EntityAttachmentRepository $attachmentRepository): Response
    {
        $attachments = $attachmentRepository->findByType($type);

        return $this->render('attachment/by_type.html.twig', [
            'attachments' => $attachments,
        ]);
    }
}
