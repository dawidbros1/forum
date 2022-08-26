<?php

namespace App\Command\Seed;

use App\Entity\Thread;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbSeedThreadCommand extends Command
{
    protected static $defaultName = 'db:seed:thread';
    protected static $defaultDescription = 'The command deletes existing threads and generates new 4 threads';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Thread::class);
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->deleteExistingThreads();

        $array = new ArrayCollection();
        $array->add(['Przywitaj się', 'Jestem nowy? Przywitaj się i opowiedź o sobie']);
        $array->add(['Problemy', 'Napotkałeś problem? Opowiedź nam o nim']);
        $array->add(['Pomysły i uwagi', 'Masz pomysły lub uwagi dotyczące forum? Podziel się z nami']);
        $array->add(['Inne', 'Nie znalazłeś odpowiedniego wątku? Napisz tutaj']);

        $this->addThreads($array);

        $io->success('Threads have been generated');
        return Command::SUCCESS;
    }

    private function deleteExistingThreads()
    {
        $threads = $this->repository->findAll() ?? [];

        foreach ($threads as $key => $thread) {
            $flush = ((count($threads) - 1) == $key);
            $this->repository->remove($thread, $flush);
        }
    }

    private function addThreads(ArrayCollection $array)
    {
        $thread = new Thread();

        foreach ($array as $key => $date) {
            $flush = ((count($array) - 1) == $key);
            $thread->setName($date[0]);
            $thread->setDescription($date[1]);
            $this->repository->add(clone $thread, $flush);
        }
    }
}
