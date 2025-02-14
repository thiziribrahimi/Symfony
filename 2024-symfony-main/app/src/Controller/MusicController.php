<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\MusicRepository;

class MusicController extends AbstractController
{
    #[Route('/', name: 'app_music')]
    public function index(
        MusicRepository $musicRepo
    ): Response
    {
        $musics = $musicRepo->findAll();

        return $this->render('music/index.html.twig', [
            'musicsList' => $musics,
        ]);
    }
}
