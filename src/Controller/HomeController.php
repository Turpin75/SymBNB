<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route(
     * "/hello/{prenom}/{age}", 
     * name="hello", 
     * requirements={"prenom":"([a-zA-Z0-9-]+)", "age":"([0-9]+)"}
     * )
     *
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0)
    {
        return $this->render(
            "hello.html.twig",
            [
                "prenom" => $prenom,
                "age" => $age
            ]
        );
        
    }
    
    /**
     * @Route("/", name="homepage")
     */
    public function home()
    {
        $prenoms = ['Lior' => 31, 'Joseph' => 30, 'Anne' => 55];

        return $this->render
        (
            "home.html.twig", 
            [
                "title" => "Bonjour tout le monde !", 
                "age" => 17,
                "prenoms" => $prenoms
            ]
        );
    }
}

?>