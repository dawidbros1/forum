<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Topic;
use App\Form\PostFormType;
use App\Form\TopicFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/topic")
 */
class TopicController extends AbstractController
{
    /**
     * @Route("/create/{topic_id}", name="topic_create")
     */
    public function create(Request $request, EntityManagerInterface $em, Security $security)
    {
        $topic = new Topic();

        $form = $this->createForm(TopicFormType::class, $topic, [
            'label' => "Dodaj temat",
            'label_attr' => [
                'class' => "fw-bold text-center fs-3",
            ],
        ]);

        $this->addButtonToForm($form, "Dodaj temat");

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic->setDate(new \DateTime());
            $topic->setUser($security->getUser());
            $em->persist($topic);
            $em->flush();

            $this->addFlash('success', 'Temat został utworzony');
            return $this->redirectToRoute('topic_create', ['topic_id' => $topic->getId()]);
        }

        return $this->render('topic/form.html.twig', [
            'title' => "Dodaj temat",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="topic_edit")
     */
    public function edit(Request $request, EntityManagerInterface $em, Security $security)
    {
        $repository = $em->getRepository(Topic::class);
        $topic = $repository->findOneBy([
            'id' => $request->get('id', 0),
            'user' => $security->getUser(),
        ]);

        $form = $this->createForm(TopicFormType::class, $topic, [
            'label' => "Edytuj temat",
            'label_attr' => [
                'class' => "fw-bold text-center fs-3",
            ],
        ]);

        $this->addButtonToForm($form, "Edytuj temat");

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($topic);
            $em->flush();
            $this->addFlash('success', 'Temat został edytowany');
            return $this->redirectToRoute('topic_edit', ['id' => $topic->getId()]);
        }

        return $this->render('topic/form.html.twig', [
            'title' => "Edytuj temat",
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="topic_show")
     */
    // public function show(Request $request, EntityManagerInterface $em)
    public function show(Topic $topic, Request $request, EntityManagerInterface $em, Security $security)
    {
        // $post = new Post();

        // $form = $this->createForm(PostFormType::class, $post, [
        //     // 'action' => $this->generateUrl('post_create', ['topic_id' => $topic->getId()]),
        //     'label' => false,
        // ]);

        // $this->addButtonToForm($form, "Dodaj posta 25");
        // $form->handleRequest($request);

        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
            // 'form' => $form->createView(),
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
    }
}
