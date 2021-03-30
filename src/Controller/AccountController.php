<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Cocur\Slugify\Slugify;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * @Route("/register", name="account_register")
     */
    public function register(EntityManagerInterface $manager, HttpFoundationRequest $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
		$user = new User();
    	$form = $this -> createForm(RegistrationType::class,$user);
    	$form -> handleRequest($request);
		if ($form->isSubmitted() && $form->isValid())
    	
    		{
                dump($user);
                $slugify = new Slugify();
                $slug = $slugify -> slugify($user -> getFirstName(). '-' . $user -> getLastName());
                $user -> setSlug($slug);
                $user -> setPassword($passwordEncoder -> encodePassword($user, $user->getPassword()));
                dump($user);
                $manager -> persist($user);
                $manager -> flush();
                $this->addFlash(
                    'success',
                    'L\'utilisateur '.$user->getFirstName().' ' . $user -> getLastName().' a été correctement enregistré.'
                );
                return $this->redirectToRoute('account_login');
            
            }


    		return $this->render('account/registration.html.twig', [
          	'form' => $form->createView()
        ]);

    }





    /**
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
    	$error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('account/login.html.twig', [
        	'hasError'=>$error
        ]);
    }

    /**
     * @Route("/account/profile", name="account_profile")
     */
    public function profile(EntityManagerInterface $manager, HttpFoundationRequest $request): Response
    {
        // recuperer l'utilisateur connecté
        $user = $this -> getUser();

    	$form = $this -> createForm(AccountType::class,$user);

    	$form -> handleRequest($request);
 
		if ($form->isSubmitted() && $form->isValid())
    	
    		{   
                $slugify = new Slugify();
                $slug = $slugify -> slugify($user -> getFirstName(). '-' . $user -> getLastName());
                $user -> setSlug($slug);
                $manager -> persist($user);
                $manager -> flush();
                $this->addFlash(
                    'success',
                    'Votre profil a bien été modifié.'
                );
                // redirect profil
                
    		}
        return $this->render('account/profile.html.twig', [
            'form' => $form->createView(),
            'user' => $this -> getUser()
        ]);    
    }

    
    /**
     * @Route("/account/", name="account_index")
     */
    public function myAccount(): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/account/password", name="account_password")
     */
    public function passwordUpdate(EntityManagerInterface $manager, HttpFoundationRequest $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        // recuperer l'utilisateur connecté
        $user = $this -> getUser();

        $passwordUpdate = new PasswordUpdate();

    	$form = $this -> createForm(PasswordUpdateType::class,$passwordUpdate);

    	$form -> handleRequest($request);
 
		if ($form->isSubmitted() && $form->isValid())
    	
    		{   
                // verif old password == user get password
                if (password_verify($passwordUpdate -> getOldPassword(), $user -> getPassword())) {
                    
                    $user -> setPassword($passwordEncoder -> encodePassword($user, $passwordUpdate -> getNewPassword()));
                    $manager -> persist($user);
                    $manager -> flush();

                    $this->addFlash(
                        'success',
                        'Le mot de passe a bien été modifié.'
                    );
                    return $this->redirectToRoute('home'); // à modifier

                } else {
                    $this->addFlash(
                        'danger',
                        'Ancien mot de passe incorrect.'
                    );
                }
                
    		}
        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'user' => $this -> getUser()
        ]);    
    	

    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout(): Response 
    {
        return $this->redirectToRoute('account_login');
    }
}
