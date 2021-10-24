<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Calculator
{

   protected $logger;

   public function __construct(LoggerInterface $logger)
   {
      $this->logger = $logger;
   }
   public function calcul($prix)
   {
      $this->logger->info("calcul tva");
      return $prix * (20/100);
   }
}