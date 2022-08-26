<?php

namespace App\Command\Thread;

use App\Entity\Thread;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbThreadCreateCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Thread::class);
        parent::__construct();
    }

    protected static $defaultName = 'db:thread:create';
    protected static $defaultDescription = 'The command adds thread';

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Name of thread')
            ->addArgument('description', InputArgument::REQUIRED, 'Description of thread')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $thread = new Thread([
            'name' => $input->getArgument("name"),
            'description' => $input->getArgument("description"),
        ]);

        $this->repository->add($thread, true);

        $io->success('The thread was added');

        return Command::SUCCESS;
    }
}
