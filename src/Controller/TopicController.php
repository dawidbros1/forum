<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Entity\Topic;
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
     * @Route("/create/{thread_id}", name="topic_create")
     */
    public function create(Request $request, EntityManagerInterface $em, Security $security)
    {
        $thread = $em->getRepository(Thread::class)->find($request->get('thread_id'));

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
            $topic->setThread($thread);
            $em->persist($topic);
            $em->flush();

            $this->addFlash('success', 'Temat został utworzony');
            return $this->redirectToRoute('topic_create', ['thread_id' => $thread->getId()]);
        }

        return $this->render('topic/form.html.twig', [
            'title' => "Dodaj temat",
            'form' => $form->createView(),
            'thread' => $thread
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
            'thread' => $topic->getThread()
        ]);
    }

    /**
     * @Route("/show/{id}", name="topic_show")
     */
    public function show(Topic $topic, Request $request, EntityManagerInterface $em, Security $security)
    {
        return $this->render('topic/show.html.twig', [
            'topic' => $topic,
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
