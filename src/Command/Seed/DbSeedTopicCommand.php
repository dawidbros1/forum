<?php

namespace App\Command\Seed;

use App\Entity\Thread;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\TextGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbSeedTopicCommand extends Command
{
    protected static $defaultName = 'db:seed:topic';
    protected static $defaultDescription = 'The command generates topics';

    public function __construct(EntityManagerInterface $entityManager, TextGenerator $textGenerator)
    {
        $this->repository = $entityManager->getRepository(Topic::class);

        $this->users = $entityManager->getRepository(User::class)->findAll();
        $this->threads = $entityManager->getRepository(Thread::class)->findAll();

        $this->textGenerator = $textGenerator;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('limit', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (empty($this->users)) {
            $io->error('Users does not exists. Use command "php bin/console db:seed:user" to generate users.');
            return Command::SUCCESS;
        }

        if (empty($this->threads)) {
            $io->error('Threads does not exists. Use command "php bin/console db:seed:thread" to generate threads.');
            return Command::SUCCESS;
        }

        $limit = $input->getArgument("limit");

        for ($i = 0; $i < $limit; $i++) {
            $this->addTopic($i == ($limit - 1));
        }

        $io->success('Topics have been generated');
        return Command::SUCCESS;
    }

    private function addTopic($flush)
    {
        $topic = new Topic;
        $topic->setTitle($this->textGenerator->generate(1, 10));
        $topic->setText($this->textGenerator->generate(5, 20));
        $topic->setUser($this->randomUser());
        $topic->setThread($this->randomThread());
        $this->repository->add($topic, $flush);
    }

    private function randomUser()
    {
        return $this->users[rand(0, count($this->users) - 1)];
    }

    private function randomThread()
    {
        return $this->threads[rand(0, count($this->threads) - 1)];
    }
}
