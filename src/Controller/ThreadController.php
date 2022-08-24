<?php

namespace App\Controller;

use App\Entity\Thread;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/thread")
 */
class ThreadController extends AbstractController
{
    /**
     * @Route("/show/{id}", name="thread_show")
     */
    public function show(Thread $thread)
    {
        return $this->render('thread/show.html.twig', [
            'thread' => $thread,
        ]);
    }
}
