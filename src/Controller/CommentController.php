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
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Comment::class);
        $this->security = $security;
    }

    /**
     * @Route("/create/{post_id}", name="comment_create")
     */
    public function create(Request $request)
    {
        $postRepository = $this->entityManager->getRepository(Post::class);

        if (!$post = $postRepository->find($request->get('post_id'))) {
            return $this->error();
        }

        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment, [
            'label' => "Tworzenie komentarza",
            'label_attr' => [
                'class' => "",
            ],
        ]);

        $this->addButtonToForm($form, "Dodaj komentarz");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setUser($this->security->getUser());
            $comment->setPost($post);
            $this->repository->add($comment, true);
            $this->addFlash('success', 'Komentarz został utworzony');
            return $this->redirectToRoute('topic_show', ['id' => $post->getTopic()->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form->createView(),
            'post' => $post,
            'title' => "Dodaj komentarz",
        ]);
    }

    /**
     * @Route("/edit/{id}", name="comment_edit")
     */
    public function edit(Comment $comment, Request $request)
    {
        if (!$this->author($comment)) {
            return $this->error();
        }

        $form = $this->createForm(CommentFormType::class, $comment);
        $this->addButtonToForm($form, "Edytuj komentarz");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->add($comment, true);
            $this->addFlash('success', 'Komentarz został edytowany');
            return $this->redirectToRoute('topic_show', ['id' => $comment->getPost()->getTopic()->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'form' => $form->createView(),
            'post' => $comment->getPost(),
            'title' => "Edytuj komentarz",
        ]);
    }

    /**
     * @Route("/my_list/", name="comment_my_list")
     */
    public function listMyComments()
    {
        return $this->render('comment/myComments.html.twig', [
            'comments' => $this->security->getUser()->getComments(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="comment_delete")
     */
    public function delete(Comment $comment, Security $security)
    {
        if (!$this->author($comment)) {
            return $this->error();
        }

        $this->repository->remove($comment, true);
        $this->addFlash('success', 'Komentarz został usunięty');
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

    private function author($comment)
    {
        return $comment->getUser()->getId() == $this->security->getUser()->getId();
    }

    private function error()
    {
        $this->addFlash('error', 'UPS! Coś poszło nie tak');
        return $this->redirectToRoute("app_homepage");
    }
}
