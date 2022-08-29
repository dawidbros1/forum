<?php

namespace App\Controller;

use App\Entity\Thread;
use App\Form\ThreadFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/thread")
 */
class ThreadController extends AbstractController
{
    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Thread::class);
        $this->security = $security;
    }

    /**
     * @Route("/create", name="thread_create")
     */
    public function create(Request $request)
    {
        $title = "Tworzenie wątka";

        $thread = new Thread();
        $form = $this->createForm(ThreadFormType::class, $thread, [
            'label' => $title,
            'label_attr' => [
                'class' => "fw-bold text-center fs-3",
            ],
        ]);

        $this->addButtonToForm($form, "Dodaj wątek");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->add($thread, true);

            $this->addFlash('success', 'Wątek został utworzony');
            return $this->redirectToRoute("app_homepage");
        }

        return $this->render('thread/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}", name="thread_edit")
     */
    public function edit(Thread $thread, Request $request)
    {
        $title = "Edycja wątka";

        $form = $this->createForm(ThreadFormType::class, $thread, [
            'label' => $title,
            'label_attr' => [
                'class' => "fw-bold text-center fs-3",
            ],
        ]);

        $this->addButtonToForm($form, "Edytuj wątek");
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repository->add($thread, true);
            $this->addFlash('success', 'Wątek został edytowany');
            return $this->redirectToRoute("app_homepage");
        }

        return $this->render('thread/form.html.twig', [
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="thread_delete")
     */
    public function delete(Thread $thread)
    {
        $this->repository->remove($thread, true);
        $this->addFlash('success', 'Wątek został usunięty');
        return $this->redirectToRoute('app_homepage');
    }

    /**
     * @Route("/show/{id}", name="thread_show")
     */
    public function show(Thread $thread)
    {
        return $this->render('thread/show.html.twig', [
            'thread' => $thread,
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
