<?php

namespace App\Command;

use App\Entity\Post;
use App\Entity\Thread;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// use App\Entity\Post;

class DbSeedThreadCommand extends Command
{
    protected static $defaultName = 'db:seed:thread';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->thread = new Thread();
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // $this->deletePosts();
        $this->deleteThreads();

        $threads = new ArrayCollection();
        $threads->add(['przywitaj się', 'to jest secjaj prziwtaj się']);
        $threads->add(['problemy', 'to jest secjaj prziwtaj się']);
        $threads->add(['general', 'to jest secjaj prziwtaj się']);
        $threads->add(['gierki', 'to jest secjaj prziwtaj się']);

        $this->addThreads($threads);

        $io->success('Dane zostały wygenerowane');
        return Command::SUCCESS;
    }

    private function deletePosts()
    {
        $postRepository = $this->em->getRepository(Post::class);
        $posts = $postRepository->findAll();
        $this->delete($postRepository, $posts);
    }

    private function deleteThreads()
    {
        $threadRepository = $this->em->getRepository(Thread::class);
        $thread = $threadRepository->findAll();
        $this->delete($threadRepository, $thread);
    }

    private function addThreads(ArrayCollection $array)
    {
        $repository = $this->em->getRepository(Thread::class);

        foreach ($array as $key => $item) {
            $flash = ((count($array) - 1) == $key);
            $this->thread->setName($item[0]);
            $this->thread->setDescription($item[1]);
            $this->thread->setDate(new \DateTime);
            $repository->add(clone $this->thread, $flash);
        }
    }

    private function delete($repository, $items)
    {
        foreach ($items as $key => $item) {
            $flash = ((count($items) - 1) == $key);
            $repository->remove($item, $flash);
        }
    }
}
