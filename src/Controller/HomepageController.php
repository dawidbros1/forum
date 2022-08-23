<?php

namespace App\Controller;

use App\Entity\Topic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(EntityManagerInterface $em): Response
    {
        $repository = $em->getRepository(Topic::class);
        $topics = $repository->findAll();

        return $this->render('homepage/index.html.twig', [
            'topics' => $topics,
        ]);
    }
}
