<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
   protected $session;
   protected $productRepository;

   public function __construct(SessionInterface $session, ProductRepository $productRepository)
   {
      $this->session = $session;
      $this->productRepository = $productRepository;
   }

   protected function getCart()
   {
      return $this->session->get('cart', []);
   }

   protected function saveCart($cart)
   {
      $this->session->set('cart', $cart);
   }

   public function add($id)
   {
      $cart = $this->getCart();

        if(!array_key_exists($id, $cart)){
            $cart[$id] = 0;
        }
         $cart[$id]++;
        

        $this->saveCart($cart);
   }

   public function getTotal()
   {
      $total = 0;

      foreach($this->getCart() as $id => $qty){
         $product = $this->productRepository->find($id);

         if(!$product){
            continue;
         }

         $total += $product->getPrice() * $qty;
     }

     return $total;
   }

   public function remove($id)
   {
      $cart = $this->getCart();

      unset($cart[$id]);

      $this->saveCart($cart);
   }

   public function decrement($id)
   {
      $cart = $this->getCart();
      
      if(!array_key_exists($id, $cart)){
         return;
      }

      if($cart[$id] === 1) {
         $this->remove($id);
         return;
      }

      $cart[$id]--;

      $this->saveCart($cart);
   }

   public function getDetailedCardItems()
   {
      $detailedCart = [];

        foreach($this->getCart() as $id => $qty){
            $product = $this->productRepository->find($id);

            if(!$product){
               continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }

        return $detailedCart;
   }
}