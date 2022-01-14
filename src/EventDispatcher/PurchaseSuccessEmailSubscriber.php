<?php

namespace App\EventDispatcher;

use App\Entity\User;
use App\Event\PurchaseSuccessEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Core\Security;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface {

   protected $mailer;
   protected $security;

   public function __construct(MailerInterface $mailer, Security $security)
   {
      $this->mailer = $mailer;
      $this->security = $security;
   }
   public static function getSubscribedEvents()
   {
      return [
         'purchase.success' => 'sendSuccessEmail'
      ];
   }

   public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent) {

      /**
       * @var User
       */
      $currentUser = $this->security->getUser();

      $purchase = $purchaseSuccessEvent->getPurchase();

      $email = new TemplatedEmail();
      $email->from(new Address("contact@mail.com"))
            ->to(new Address($currentUser->getEmail(), $currentUser->getFullName()))
            ->htmlTemplate('emails/purchase_success.html.twig')
            ->context([
               'purchase' => $purchase,
               'user' => $currentUser
            ])
            ->subject("Confirmation de la commande n° " . $purchase->getId());

      $this->mailer->send($email);

   }
}