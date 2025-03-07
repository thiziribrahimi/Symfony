<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MusicRepository;
use App\Form\MusicType;
use App\Entity\Music;
use Symfony\Component\HttpFoundation\Request; 
use Doctrine\ORM\EntityManagerInterface;

final class MusicController extends AbstractController
{
    #[Route('/', name: 'app_music')] 
    public function index(
         MusicRepository $musicRepo
    ): Response
    {
        $music = $musicRepo->findAll();
        return $this->render('music/index.html.twig', [
        'musicsList' => $music,
        ]);
    }

    #[Route('/music/new', name: 'app_music_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response

    {
        $music = new Music();
        $form = $this->createForm(MusicType::class, $music);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
        
            $music = $form->getData();

            $entityManager->persist($music);
            $entityManager->flush();

            return $this->redirectToRoute('app_music');
        }

        return $this->render('music/new.html.twig',[
            'form' => $form,

        ]);
    }


}