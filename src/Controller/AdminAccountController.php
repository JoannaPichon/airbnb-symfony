<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{

    /**
     * @Route("/admin/login", name="admin_account_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
    	$error = $authenticationUtils->getLastAuthenticationError();

        return $this->render('admin/account/login.html.twig', [
        	'hasError'=>$error
        ]);
    }

    /**
     * @Route("/admin/account/profile", name="admin_account_profile")
     */
    public function profile(): Response
    {
        
        return $this->render('admin/account/profile.html.twig', [
            
            'user' => $this -> getUser()
        ]);    
    }

    /**
     * @Route("/admin/logout", name="admin_account_logout")
     */
    public function logout()
    {
           
    }
}
