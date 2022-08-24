<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/comment")
 */
class CommentController extends AbstractController
{
    /**
     * @Route("/create/{post_id}", name="comment_create")
     */
    public function create(Request $request, Security $security, EntityManagerInterface $em)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment, [
            'label' => "Tworzenie komentarza",
            'label_attr' => [
                'class' => "",
            ],
        ]);
        $this->addButtonToForm($form, "Dodaj komentarz");
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
            return $this->redirectToRoute('topic_show', ['id' => $post->getTopic()->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'title' => "Dodaj komentarz"
        ]);
    }

    /**
     * @Route("/edit/{id}", name="comment_edit")
     */
    public function edit(Comment $comment, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(CommentFormType::class, $comment);
        $this->addButtonToForm($form, "Edytuj komentarz");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();
            $this->addFlash('success', 'Komentarz został edytowany');
            return $this->redirectToRoute('topic_show', ['id' => $comment->getPost()->getTopic()->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form->createView(),
            'post' => $comment->getPost(),
            'title' => "Edytuj komentarz"
        ]);
    }

    /**
     * @Route("/my_list/", name="comment_my_list")
     */
    public function listMyComments(Request $request, EntityManagerInterface $em, Security $security)
    {
        $repository = $em->getRepository(Comment::class);
        $comments = $repository->findBy(['user' => $security->getUser()]);

        return $this->render('comment/myComments.html.twig', [
            'comments' => $comments,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="comment_delete")
     */
    public function delete(Comment $comment, EntityManagerInterface $em)
    {
        $repository = $em->getRepository(Comment::class);
        $repository->remove($comment, true);
        return $this->redirectToRoute("topic_show", ['id' => $comment->getPost()->getTopic()->getId()]);
    }
    private function addButtonToForm($form, string $label)
    {
        $form->add('submit', SubmitType::class, [
            'label' => $label,
            'attr' => [
                'class' => "btn-primary w-100 fw-bold",
            ],
        ]);

        return $form;
    }
}
