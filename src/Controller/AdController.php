<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use Cocur\Slugify\Slugify;
use App\Entity\ImageUpload;
use Doctrine\ORM\Mapping\Id;
use App\Repository\AdRepository;
use App\Services\ImagesUploadService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * @Route("/ads/{page}", name="ads_index", requirements={"page":"[0-9]{1,}"})
     */
    public function index(AdRepository $repo, $page=1): Response
    {   
        $limit = 10;
        $offset = $page * $limit - $limit ;
        $pages = ceil(count($repo->findAll())/$limit);

        //$repo = $this   -> getDoctrine()-> getRepository(Ad::class);
        $ads = $repo -> findBy(array(), ['id' => 'ASC'], $limit, $offset);

        return $this->render('ad/index.html.twig', [
            'page' => $page,
            'pages' => $pages,
            'ads' => $ads
        ]);
    }

    
    /**
     * @IsGranted("ROLE_USER")
     * @Route("/ads/new", name="ad_new")
     */
    public function create(EntityManagerInterface $manager, Request $request, ImagesUploadService $upload ): Response
    {
        $ad = new Ad();
        // $image = new Image();
        // $image      -> setUrl("https://picsum.photos/seed/200/300")
        //             -> setCaption('coucou'); 
        // $ad -> addImage($image);
        // $image2 = new Image();
        // $image2     -> setUrl("https://picsum.photos/seed/200/300")
        //             -> setCaption('legende'); 
        // $ad -> addImage($image2);

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
            
            //fonction upload de fichier déportée dans Services 
            $upload -> upload($ad, $manager);

            $ad->setAuthor($this->getUser());
            $manager -> persist($ad);
            $manager -> flush();

            $ad -> setSlug($ad -> getSlug().'_'. $ad -> getId());

            $manager -> persist($ad);
            $manager -> flush();

            $this->addFlash(
                'success',
                'L\'annonce <strong>' . $ad -> getTitle() . '</strong> a bien été enregistrée.'
            );

            return $this -> redirectToRoute('ad_show', ['slug' => $ad -> getSlug()]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form -> createView(),
            'ad'    => $ad
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message ="Cette annonce ne vous appartient pas")
     * @Route("/ads/{slug}/edit", name="ad_edit")
     * 
     */
    public function edit(EntityManagerInterface $manager, Request $request, Ad $ad, ImagesUploadService $upload): Response
    {
        
        
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

           return $this -> redirectToRoute('ad_show', ['slug' => $ad -> getSlug()]);
        }

        return $this->render('ad/edit.html.twig', [
            'form' => $form -> createView(),
            'ad' => $ad
        ]);
    }



    /**
     * @Route("/ads/{slug}", name="ad_show")
     */
    public function show(Ad $ad): Response
    {

        //$repo = $this   -> getDoctrine()-> getRepository(Ad::class);
        //$ads = $repo -> findAll();
        //dump($ads);

        
        //$ad = $repo -> findOneBySlug($slug);
        //dump($ad);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message ="Cette annonce ne vous appartient pas, vous ne pouvez pas la supprimer")
     * @Route("/ads/{slug}/delete", name="ad_delete")
     */
    public function delete(EntityManagerInterface $manager, Ad $ad): Response
    {   
        $manager -> remove($ad);
        $manager -> flush();

        // supprimer images sur serveur a la suppression de lannonce
        // (version facile)
        // $images = $ad -> getImageUploads();
        // foreach ($images as $image) {
        //     unlink($_SERVER['DOCUMENT_ROOT'] .  $image -> getUrl());
        // }


        $this->addFlash(
            'success',
            'L\'annonce a bien été supprimée.'
        );

        return $this -> redirectToRoute('ads_index');
    }




    
}
