<?php

namespace App\Controller\Admin;

use App\Entity\Music;
use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AsDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

#[AsDashboard] // Correction de l'attribut (anciennement AdminDashboard)
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')] // Déclare proprement ta route ici
    public function index(): Response
    {
        // Redirection vers la liste des musiques (ou autre entité par défaut)
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $url = $adminUrlGenerator->setController(MusicCrudController::class)->generateUrl();

        return $this->redirect($url);

        // OU si tu veux afficher un template personnalisé (comme tu le faisais)
        // return $this->render('admin/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('🎶 Music Dashboard');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Music', 'fas fa-music', Music::class);
        yield MenuItem::linkToCrud('Catégories', 'fas fa-folder', Category::class);
    }
}
