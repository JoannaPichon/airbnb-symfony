<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;
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
        $ads = $repo->findBestAds();
    


        // trier ad par nb de resa (count(ad->getbookings))
        // - recup les 3 premiers

        dump($ads);

        $adsMostBooked = $repo->findBestAuthors();

        
        
        
        return $this->render('home/index.html.twig', [
            'ads' => $ads,
            'adsMostBooked' => $adsMostBooked
        ]); 
    }
}
