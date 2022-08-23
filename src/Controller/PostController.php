<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Topic;
use App\Form\CommentFormType;
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
    /**
     * @Route("/create/{topic_id}", name="post_create")
     */
    public function create(Request $request, EntityManagerInterface $em, Security $security)
    {
        $topicRepository = $em->getRepository(Topic::class);
        $topic = $topicRepository->find($request->get('topic_id'));

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
            $post->setDate(new \DateTime());
            $post->setUser($security->getUser());
            $post->setTopic($topic);
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'Post został utworzony');
            return $this->redirectToRoute('topic_show', ['id' => $topic->getId()]);
        }

        return $this->render('post/form.html.twig', [
            'title' => "Dodaj temat",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="post_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, Security $security)
    {
        $repository = $em->getRepository(Post::class);
        $post = $repository->findOneBy([
            'id' => $request->get('id', 0),
            'user' => $security->getUser(),
        ]);

        $form = $this->createForm(PostFormType::class, $post, [
            'label' => "Edytuj temat",
            'label_attr' => [
                'class' => "fw-bold text-center fs-3",
            ],
        ]);

        $this->addButtonToForm($form, "Edytuj temat");

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($post);
            $em->flush();
            $this->addFlash('success', 'Post został edytowany');
            return $this->redirectToRoute('post_edit', ['id' => $post->getId()]);
        }

        return $this->render('post/form.html.twig', [
            'title' => "Edytuj temat",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/list", name="post_list")
     */
    function list(Request $request, EntityManagerInterface $em) {
        $category_id = $request->get('category_id', 0);
        $repository = $em->getRepository(Category::class);
        $category = $repository->find($category_id);

        return $this->render('post/list.html.twig', [
            'category_name' => $category->getName(),
            'posts' => $category->getPosts(),
        ]);
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

    /**
     * @Route("/show/{id}", name="post_show")
     */
    // public function show(Request $request, EntityManagerInterface $em)
    public function show(Post $post, Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment, [
            'action' => $this->generateUrl('comment_create', ['post_id' => $post->getId()]),
            'label' => false,
        ]);

        $form->handleRequest($request);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'form' => $form->createView(),
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
}
