<?php

namespace App\Command\Thread;

use App\Entity\Thread;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbThreadRemoveCommand extends Command
{
    protected static $defaultName = 'db:thread:remove';
    protected static $defaultDescription = 'The command removes thread';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Thread::class);
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of thread');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $name = $input->getArgument('name');

        $thread = $this->repository->findOneBy(['name' => $name]);

        if ($thread == null) {
            $io->error("The thread with name [$name] do not exists");
        } else {
            $this->repository->remove($thread, true);
            $io->success('The thread was removed');
        }

        return Command::SUCCESS;
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
