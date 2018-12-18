<?php

namespace App\Service;

use Twig\Environment;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;

class Pagination
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $template;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $template)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->template = $template;
    }
    
    public function getData()
    {
        if(empty($this->entityClass))
        {
            throw new \Exception("Class sur laquelle paginer non rensignée !
            Utilisez la méthode setEntityClass() de votre objet Pagination !");
        }
        
        // 1)Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // 2)Demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        // 3)Renvoyer les éléments en question
        return $data;

    }

    public function getPages()
    {
        if(empty($this->entityClass))
        {
            throw new \Exception("Class sur laquelle paginer non rensignée !
            Utilisez la méthode setEntityClass() de votre objet Pagination !");
        }
        
        // 1) Connaître le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // 2) Faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    public function display()
    {
        $this->twig->display($this->template, 
        [
           'page' => $this->currentPage,
           'pages' => $this->getPages(),
           'route' => $this->route
        ]);
    }
    
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        
        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }
    
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    public function getTemplate()
    {
        return $this->template();
    }

    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }
}