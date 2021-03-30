<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page=1): Response
    {
        $limit = 10;
        $offset = $page * $limit - $limit ;
        $pages = ceil(count($repo->findAll())/$limit);

        //$repo = $this   -> getDoctrine()-> getRepository(Ad::class);
        $ads = $repo -> findBy(array(), ['id' => 'ASC'], $limit, $offset);

        return $this->render('admin/ad/index.html.twig', [
            'page' => $page,
            'pages' => $pages,
            'ads' => $ads
        ]);
    }
}
