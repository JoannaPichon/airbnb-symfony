<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AdRepository $repo, EntityManagerInterface $manager): Response
    {
        $ads = $repo->findBestAds();
        dump($ads);

        $adsMostBooked = $repo->findBestAuthors();

        foreach ($adsMostBooked as $ad) {
            dump(count($ad->getBookings()));
        }


        $bestAds = $manager->createQuery(
            'SELECT avg(c.rating) as note, a.title, a.id, u.firstName FROM App\Entity\Comment c
             JOIN c.ad a
             JOIN a.author u
             GROUP BY a
             ORDER BY note DESC')
             ->setMaxResults(3)
             ->getResult()
        ;

        dump($bestAds);


        
        return $this->render('home/index.html.twig', [
            'ads' => $ads,
            'adsMostBooked' => $adsMostBooked
        ]); 
    }
}
