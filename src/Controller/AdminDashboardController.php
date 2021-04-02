<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard_index")
     */
    public function index(EntityManagerInterface $manager): Response
    {
        $users = $manager   ->createQuery("SELECT count(u) FROM App\Entity\User u")
                            ->getSingleScalarResult();
        dump($users);

        $ads = $manager   ->createQuery("SELECT count(a) FROM App\Entity\Ad a")
                            ->getSingleScalarResult();
        dump($ads);

        $bookings = $manager   ->createQuery("SELECT count(b) FROM App\Entity\Booking b")
                            ->getSingleScalarResult();
        dump($bookings);

        $ratings = $manager   ->createQuery("SELECT count(r) FROM App\Entity\Comment r")
                            ->getSingleScalarResult();
        dump($ratings);


        return $this->render('admin/dashboard/index.html.twig', [
            'users' => $users,
            'ads' => $ads,
            'bookings' => $bookings,
            'ratings' => $ratings,
        ]);
    }
}
