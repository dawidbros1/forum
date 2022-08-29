<?php

namespace App\Command\General;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbTableClearCommand extends Command
{
    protected static $defaultName = 'db:table:clear';
    protected static $defaultDescription = 'The command remove content of table by entity name';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('table', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $table = ucfirst($input->getArgument("table"));

        if (!$repository = $this->getRepository($table)) {
            $io->error('Invalid name of table:' . $table);
            return Command::SUCCESS;
        }

        $items = $repository->findAll();

        foreach ($items as $index => $item) {
            $repository->remove($item, $index == (count($items) - 1));
        }

        $io->success($table . 's have been deleted');

        return Command::SUCCESS;
    }

    public function getRepository(string $table)
    {
        if (in_array($table, ['User', 'Thread', 'Topic', 'Post', 'Comment'])) {
            $namespace = "App\Entity\\" . $table;
            return $this->entityManager->getRepository($namespace);
        }
        return null;
    }
}
