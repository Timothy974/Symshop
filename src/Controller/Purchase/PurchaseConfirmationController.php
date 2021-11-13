<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PurchaseConfirmationController extends AbstractController
{
   protected $cartService;
   protected $em;

   public function __construct(EntityManagerInterface $em,  CartService $cartService)
   {
      $this->cartService = $cartService;
      $this->em = $em;
   }

   /**
    * @Route("/purchase/confirm", name="purchase_confirm")
    * @IsGranted("ROLE_USER", message="Vous devez être connecté pour confirmer une commande")
    *
    * @return void
    */
   public function confirm(Request $request, FlashBagInterface $flashBag)
   {
      $form = $this->createForm(CartConfirmationType::class);

      $form->handleRequest($request);

      if(!$form->isSubmitted()){

         $this->addFlash('warning', 'vous devez remplir le formulaire de confirmation');
         return $this->redirectToRoute('cart_show');
      }

      $user = $this->getUser();

      $cartItems = $this->cartService->getDetailedCardItems();

      if (count($cartItems) === 0) {
         $this->addFlash('warning', "Vous ne pouvez confirmer une commande avec un panier vide");
         return $this->redirectToRoute('cart_show');
      }

      $purchase = $form->getData();

      $purchase->setUser($user)
               ->setPurchasedAt(new DateTime())
               ->setTotal($this->cartService->getTotal());
      
      $this->em->persist($purchase);

      foreach ($this->cartService->getDetailedCardItems() as $cartItem) {

         $purchaseItem = new PurchaseItem();
         $purchaseItem->setPurchase($purchase)
                     ->setProduct($cartItem->product)
                     ->setProductName($cartItem->product->getName())
                     ->setProductPrice($cartItem->product->getPrice())
                     ->setQuantity($cartItem->qty)
                     ->setTotal($cartItem->getTotal());

         $this->em->persist($purchaseItem);
      }

      $this->em->flush();

      $this->cartService->empty();

      $this->addFlash('success', "La commande a bien été enregistrée");
      return $this->redirectToRoute('purchase_index');
   }
}