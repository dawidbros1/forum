<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/create", name="comment_create")
     */
    public function create(Request $request, Security $security, EntityManagerInterface $em)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        $postRepository = $em->getRepository(Post::class);
        $post = $postRepository->find($request->get('post_id'));

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setDate(new \DateTime());
            $comment->setUser($security->getUser());
            $comment->setPost($post);
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Komentarz został utworzony');
        }

        return $this->redirectToRoute('post_show', ['id' => $post->getId()]);
    }

    /**
     * @Route("/delete/{id}", name="comment_delete")
     */
    public function delete(Comment $comment, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Comment::class);
        $repository->remove($comment, true);
        return $this->redirectToRoute("post_show", ['id' => $comment->getPost()->getId()]);
    }
}
