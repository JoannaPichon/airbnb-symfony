<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Image;
use App\Entity\Role;
use Cocur\Slugify\Slugify;
use DateTime;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        //creation dun admin
        $adminUser = new User();
        $adminUser   -> setFirstName('Joanna')
                    -> setLastName('Pichon')
                    -> setEmail('mail@admin.fr')
                    -> setPicture('https://www.w3schools.com/howto/img_avatar.png')
                    -> setPassword($this->passwordEncoder->encodePassword($adminUser,'heyheyhey'))
                    -> setIntroduction('Superbe appartement en plein coeur du centre ville de Paris')
                    -> setDescription('Réservez un séjour pleines de surprises dans ce merveilleux appartement, avec vue splendide sur la station de RER nommée "Châtelet", d\'une taille de 13 mètres carrés.<br/>
            Grâce à son spacieux salon dune taille de 5 mètres carrés, vous pourrez profiter d\'un espace de vie "cocooning" où tout est à portée de main. <br/>
                Appréciez aussi le doux bruit des voisins qui ont la bienveillance de vous faire profiter du son haut de gamme de leur télévision Leader Price, en particulier durant les matchs de foot.<br/>
                Bénéficiant d\'une magnifique baignoire tout-en-un d\'une longueur de 95 centimètres, observez la praticité que vous apporte la possibilité de se laver, faire ses besoins et se brosser les dents, tout ça, au même endroit.<br/>
                Avec l\'option "Bed & Breakfast", disponible pour un surcoût de seulement 25€ par personne, vous pourrez bénéficier d\'un petit déjeuner, déposé avec amour sur le paillasson, comprenant une baguette de pain et un croissant. <br/>
                Sur la table basse du salon, qui fait aussi office de table à repasser, vous trouverez à votre disposition un guide pour découvrir les meilleurs coins du quartier, comme par exemple le bois de Boulogne, où les meilleurs guides de Paris sauront vous conseiller pour votre balade.')
                    -> setSlug('joanna-pichon')
                    -> addUserRole($adminRole);
        $manager->persist($adminUser);

        $slugify = new Slugify();
        $title = "titré de l'ànnonçe n°!";
        $slug = $slugify -> slugify($title);


        for ($k=0; $k <= 5; $k++) { 
            $user = new User();
            $user   -> setFirstName('Jean'.$k)
                    -> setLastName('Dupond'.$k)
                    -> setEmail('jean'.$k.'.dupond'.$k.'@mail.fr')
                    -> setPicture('https://www.w3schools.com/howto/img_avatar.png')
                    -> setPassword($this->passwordEncoder->encodePassword($user,'password'))
                    -> setIntroduction('Superbe appartement en plein coeur du centre ville de Paris')
                    -> setDescription('Réservez un séjour pleines de surprises dans ce merveilleux appartement, avec vue splendide sur la station de RER nommée "Châtelet", d\'une taille de 13 mètres carrés.<br/>
            Grâce à son spacieux salon dune taille de 5 mètres carrés, vous pourrez profiter d\'un espace de vie "cocooning" où tout est à portée de main. <br/>
                Appréciez aussi le doux bruit des voisins qui ont la bienveillance de vous faire profiter du son haut de gamme de leur télévision Leader Price, en particulier durant les matchs de foot.<br/>
                Bénéficiant d\'une magnifique baignoire tout-en-un d\'une longueur de 95 centimètres, observez la praticité que vous apporte la possibilité de se laver, faire ses besoins et se brosser les dents, tout ça, au même endroit.<br/>
                Avec l\'option "Bed & Breakfast", disponible pour un surcoût de seulement 25€ par personne, vous pourrez bénéficier d\'un petit déjeuner, déposé avec amour sur le paillasson, comprenant une baguette de pain et un croissant. <br/>
                Sur la table basse du salon, qui fait aussi office de table à repasser, vous trouverez à votre disposition un guide pour découvrir les meilleurs coins du quartier, comme par exemple le bois de Boulogne, où les meilleurs guides de Paris sauront vous conseiller pour votre balade.')
                    -> setSlug('Jean'.$k.'-'.'Dupond'.$k);



                    
            $manager -> persist($user);

                    

            for ($i=1; $i <= mt_rand(1,5) ; $i++) { 

            
                $ad = new Ad();
                $ad -> setTitle('Titre de l\'annonce n°' . $i)
                    -> setSlug($slug . $i)
                    -> setCoverImage('https://picsum.photos/seed/300/200')
                    -> setIntroduction('<strong>Introduction</strong> de l\'annonce ' . $i)
                    -> setContent('<p>Contenu de l\'annonce ' . $i)
                    -> setPrice(mt_rand(30,150))
                    -> setRooms(mt_rand(1,5))
                    -> setAuthor($user);
        
                    //ajout images à une annonce
                for ($j=0; $j < mt_rand(2,5); $j++) { 
                    $image = new Image();
                    $image  -> setUrl('https://picsum.photos/seed/200/200')
                            -> setCaption('Légende de l\'image '. $j)
                            -> setAd($ad);
                    $manager -> persist($image);
                }
        
                $manager -> persist($ad);
                $manager -> flush();
        
                $slug2 = $ad -> getSlug().'_'.$ad -> getId();
                $ad -> setSlug($slug2);
        
                $manager -> persist($ad);
                
                //mettre toutes les dates à minuit 00:00:00 !!
                for ($j=0; $j < mt_rand(0,5) ; $j++) { 
                    $booking = new Booking();
                    $startDate = new \DateTime("+ 5 days");
                    $startDate->setTime(0,0,0,0);
                    $endDate = new \DateTime("+ 12 days");
                    $endDate->setTime(0,0,0,0);
                    $booking->setAd($ad)
                            ->setBooker($user)
                            ->setStartDate($startDate)
                            ->setEndDate($endDate)
                            ->setCreatedAt(new \DateTime())
                            ->setAmount($ad->getPrice()*7)
                            ->setComment("N'oubliez pas le lit pour bébé svp");
                    $manager->persist($booking);
                }
                if (mt_rand(0,1)) {
                    $comment = new Comment();
                    $comment->setCreatedAt(new \DateTime())
                            ->setRating(mt_rand(0,5))
                            ->setContent("Super séjour $j")
                            ->setAuthor($user)
                            ->setAd($ad);
                    $manager->persist($comment);
                }
            }        
        }
    
    } 
}