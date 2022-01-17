<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('bundles/EasyAdminBundles/welcome.html.twig');
        //return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symshop Boutique Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Produit', 'fa fa-gifts', Product::class);
        yield MenuItem::linkToCrud('Commande', 'fa fa-shopping-cart', Purchase::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tag', Category::class);
        yield MenuItem::linkToCrud('Utilisateur', 'fa fa-users', User::class);
        yield MenuItem::linkToRoute('Accueil Boutique', 'fa fa-store', 'homepage');
    }
}
