<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AdRepository $repo): Response
    {
        $ads = $repo->findAll();
        // afficher meilleurs ad (note)
        //best proprio (nb resa)
        
        return $this->render('home/index.html.twig', [
            'ads' => $ads
        ]);
    }
}
