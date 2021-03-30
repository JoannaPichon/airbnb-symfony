<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comment/{page}", name="admin_comments_index")
     */
    public function index(CommentRepository $repo, $page = 1): Response
    {

        $limit = 10;
        $offset = $page * $limit - $limit ;
        $pages = ceil(count($repo->findAll())/$limit);

        //$repo = $this   -> getDoctrine()-> getRepository(Ad::class);
        $comments = $repo -> findBy(array(), ['id' => 'ASC'], $limit, $offset);


        return $this->render('admin/comment/index.html.twig', [
            'page' => $page,
            'pages' => $pages,
            'comments' => $comments,
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/comment/{id}/edit", name="admin_comment_edit")
     */
    public function edit(Comment $comment, EntityManagerInterface $manager, Request $request): Response
    {
        
        $form = $this -> createForm(CommentType::class, $comment);
        $form -> handleRequest($request);

        if ($form -> isSubmitted() && $form -> isValid()) {

            $comment->setCreatedAt(new \DateTime());


            $manager -> persist($comment);
            $manager -> flush();

            $this->addFlash(
                'success',
                'Le commentaire a bien été modifié.'
            );

           return $this -> redirectToRoute('admin_comments_index');
        }

        return $this->render('admin/comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     */
    public function delete(EntityManagerInterface $manager, Comment $comment): Response
    {
        $manager -> remove($comment);
        $manager -> flush();

        $this->addFlash(
            'success',
            'Le commentaire a bien été supprimé.'
        );

        return $this -> redirectToRoute('admin_comments_index');
    }
}
