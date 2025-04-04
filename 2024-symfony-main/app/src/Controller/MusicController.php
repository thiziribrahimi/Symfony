<?php

namespace App\Controller;

use App\Entity\Music;
use App\Form\MusicType;
use App\Repository\MusicRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/music')]
class MusicController extends AbstractController
{
    #[Route('/new', name: 'app_music_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
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

        return $this->render('music/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/', name: 'app_music')]
    public function index(MusicRepository $musicRepository): Response
    {
        $musics = $musicRepository->findAll();

        return $this->render('music/index.html.twig', [
            'musicsList' => $musics,
        ]);
    }
}
