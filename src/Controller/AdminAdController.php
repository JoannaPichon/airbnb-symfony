<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use Cocur\Slugify\Slugify;
use App\Repository\AdRepository;
use App\Services\ImagesUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/ad/{slug}/edit", name="admin_ad_edit")
     */
    public function edit(Ad $ad, EntityManagerInterface $manager, Request $request, ImagesUploadService $upload): Response
    {
        dump($ad->getImageUploads());
        
        $form = $this -> createForm(AnnonceType::class, $ad);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {

            $slugify = new Slugify();
            $slug = $slugify -> slugify($ad -> getTitle());
            $ad -> setSlug($slug);

            foreach ($ad->getImages() as $image) {
                $image -> setAd($ad);
                dump($image);
                $manager -> persist($image);
            }

            $upload -> upload($ad, $manager);

            dump($ad->idArray);

            $ids = $ad -> idArray;
            $ids = preg_replace("#^,#", '', $ids);
            $idArray = explode(",", $ids);
            // dump($ad -> getImageUploads());
            // dump($_SERVER);
            foreach ($idArray as $id) {
                $imagesUpload = $ad -> getImageUploads();
                foreach ($imagesUpload as $image) {
                    if ($image -> getId() == $id) {
                        $manager -> remove($image); 
                        $manager -> flush();
                        // dump($_SERVER['DOCUMENT_ROOT'] .  $image -> getUrl());
                        unlink($_SERVER['DOCUMENT_ROOT'] .  $image -> getUrl());
                    }
                }
            }

            $manager -> persist($ad);
            $manager -> flush();

            $ad -> setSlug($ad -> getSlug().'_'. $ad -> getId());

            $manager -> persist($ad);
            $manager -> flush();

            $this->addFlash(
                'success',
                'L\'annonce <strong>' . $ad -> getTitle() . '</strong> a bien été modifiée.'
            );

           return $this -> redirectToRoute('admin_ads_index');
        }

        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/ad/{slug}/delete", name="admin_ad_delete")
     */
    public function delete(EntityManagerInterface $manager, Ad $ad): Response
    {
        $manager -> remove($ad);
        $manager -> flush();

        $this->addFlash(
            'success',
            'L\'annonce a bien été supprimée.'
        );

        return $this -> redirectToRoute('admin_ads_index');
    }
}
