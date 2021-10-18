<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController
{
   public function index ()
   {
      dd("hello");
      
   }

/**
 * @Route("/test/{age<\d+>?0}", name="test", methods={"GET, "POST"})
 *
 * @param Request $request
 * @param [type] $age
 * @return void
 */
   public function test (Request $request, $age)
   {

      dump($request);
      
      
      return new Response("vous avez $age ans");
   }
}