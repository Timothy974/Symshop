<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
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
   protected $persister;


   public function __construct(EntityManagerInterface $em, CartService $cartService, PurchasePersister $persister)
   {
      $this->cartService = $cartService;
      $this->em = $em;
      $this->persister = $persister;
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

      $this->persister->storePurchase($purchase);

      return $this->redirectToRoute('purchase_payment_form', [
         'id' => $purchase->getId()
      ]);
   }
}