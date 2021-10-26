<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie n'éxiste pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show")
     *
     *
     */
    public function show($slug, ProductRepository $productRepository)
    {

        $product = $productRepository->findOneBy([
            'slug' => $slug
        ]);

        if (!$product) {
            throw $this->createNotFoundException("Le produit n'éxiste pas");
        }

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     *
     * @return void
     */
    public function edit($id, ProductRepository $productRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em) 
    {

        $product = $productRepository->find($id);

        $form = $this->createForm(ProductType::class, $product);

        

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $product->setSlug($slugger->slug($product->getName()));

            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ], 302);
        }

        $formView = $form->createView();

        return $this->render('product/edit.html.twig', [
                'formView' => $formView,
                'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     *
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setSlug(strtolower($slugger->slug($product->getName())));

            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ], 302);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }
}
