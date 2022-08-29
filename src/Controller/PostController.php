<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/post")
 */
class PostController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Post::class);
        $this->security = $security;
    }

    /**
     * @Route("/create/{topic_id}", name="post_create")
     */
    public function create(Request $request)
    {
        if (!$topic = $this->entityManager->getRepository(Topic::class)->find($request->get('topic_id'))) {
            return $this->error();
        }

        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post, [
            'label' => "Tworzenie posta",
            'label_attr' => [
                'class' => "fw-bold text-center fs-3",
            ],
        ]);

        $this->addButtonToForm($form, "Dodaj posta");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $post->setUser($this->security->getUser());
            $post->setTopic($topic);
            $this->repository->add($post, true);
            $this->addFlash('success', 'Post został utworzony');
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }

        return $this->render('post/form.html.twig', [
            'title' => "Tworzenie posta",
            'form' => $form->createView(),
            'topic' => $topic,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="post_edit")
     */
    public function edit(Post $post, Request $request)
    {
        if (!$this->author($post)) {return $this->error();}

        $form = $this->createForm(PostFormType::class, $post, [
            'label' => "Edytuj temat",
            'label_attr' => [
                'class' => "fw-bold text-center fs-3",
            ],
        ]);

        $this->addButtonToForm($form, "Edytuj temat");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->add($post, true);
            $this->addFlash('success', 'Post został edytowany');
            return $this->redirectToRoute('topic_show', ['id' => $post->getTopic()->getId()]);
        }

        return $this->render('post/form.html.twig', [
            'title' => "Educja posta",
            'form' => $form->createView(),
            'topic' => $post->getTopic(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="post_delete")
     */
    public function delete(Post $post)
    {
        if (!$this->author($post)) {return $this->error();}

        $this->repository->remove($post, true);
        $this->addFlash('success', 'Post został usunięty');
        return $this->redirectToRoute('topic_show', ['id' => $post->getTopic()->getId()]);
    }

    /**
     * @Route("/my_list/", name="post_my_list")
     */
    public function listMyPosts(Request $request, EntityManagerInterface $em, Security $security)
    {
        $repository = $em->getRepository(Post::class);
        $posts = $repository->findBy(['user' => $security->getUser()]);

        return $this->render('post/myPosts.html.twig', [
            'posts' => $posts,
        ]);
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
