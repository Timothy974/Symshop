<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Stripe\StripeService;
use App\Repository\PurchaseRepository;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{

   /**
    * @Route("/purchase/pay/{id}", name="purchase_payment_form")
    * @IsGranted("ROLE_USER")
    *
    * @return void
    */
   public function ShowCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
   {
      $purchase = $purchaseRepository->find($id);

      if(
         !$purchase ||
         ($purchase && $purchase->getUser() !== $this->getUser()) ||
         ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
         ) {
      return $this->redirectToRoute('cart_show');
      }

      $intent = $stripeService->getPaymentIntent($purchase);

      $user = $purchase->getUser()->getFullName();

      return $this->render('purchase/payment.html.twig', [
         'clientSecret' => $intent->client_secret,
         'user' => $user,
         'purchase' => $purchase,
         'stripePublicKey' => $stripeService->getPublicKey()
      ]);
   }
}