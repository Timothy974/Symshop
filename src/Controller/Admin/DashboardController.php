<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Repository\CategoryRepository;
use App\Repository\PurchaseRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    protected $categoryRepository;
    protected $purchaseRepository;

    public function __construct(CategoryRepository $categoryRepository, PurchaseRepository $purchaseRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->purchaseRepository = $purchaseRepository;
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $categories = $this->categoryRepository->findAll();
        $purchases = $this->purchaseRepository->countByDate();

        $categoryName = [];
        $categoryCount = [];
        $categoryColor = [];

        foreach ($categories as $category) {
            $categoryName[] = $category->getName();
            $categoryCount[] = count($category->getProducts());
            $categoryColor[] = $category->getColor();
        }
 
        $purchaseDate = [];
        $purchaseCount = [];

        foreach ($purchases as $purchase) {
            $purchaseDate[] = $purchase['purchaseDate'];
            $purchaseCount[] = $purchase['count'];
        }



        return $this->render('bundles/EasyAdminBundles/welcome.html.twig', [
            "categoryName" => json_encode($categoryName),
            "categoryCount" => json_encode($categoryCount),
            "categoryColor" => json_encode($categoryColor),
            "purchaseDate" => json_encode($purchaseDate),
            "purchaseCount" => json_encode($purchaseCount),
        ]);
        //return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symshop Boutique Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToCrud('Produits', 'fa fa-gifts', Product::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-shopping-cart', Purchase::class);
        yield MenuItem::linkToCrud('Catégories', 'fa fa-tag', Category::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
        yield MenuItem::linkToRoute('Accueil Boutique', 'fa fa-store', 'homepage');
    }
}
