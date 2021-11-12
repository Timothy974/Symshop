<?php

namespace App\Cart;

use App\Entity\Product;

class CartItem
{
   public $product;
   public $qty;

   public function __construct(Product $product, $qty)
   {
      $this->product = $product;
      $this->qty = $qty;
   }

   public function getTotal(){

      return $this->product->getPrice() * $this->qty;
   }
}