<?php

namespace App\Command\Seed;

use App\Entity\Post;
use App\Entity\Topic;
use App\Entity\User;
use App\Service\TextGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbSeedPostCommand extends Command
{
    protected static $defaultName = 'db:seed:post';
    protected static $defaultDescription = 'The command generates posts';

    public function __construct(EntityManagerInterface $entityManager, TextGenerator $textGenerator)
    {
        $this->repository = $entityManager->getRepository(Post::class);

        $this->users = $entityManager->getRepository(User::class)->findAll();
        $this->topics = $entityManager->getRepository(Topic::class)->findAll();

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

        if (empty($this->topics)) {
            $io->error('Topics does not exists. Use command "php bin/console db:seed:topic limit" to generate topics.');
            return Command::SUCCESS;
        }

        $limit = $input->getArgument("limit");

        for ($i = 0; $i < $limit; $i++) {
            $this->addPost($i == ($limit - 1));
        }

        $io->success('Posts have been generated');
        return Command::SUCCESS;
    }

    private function addPost($flush)
    {
        $post = new Post();
        $post->setText($this->textGenerator->generate(5, 20));
        $post->setUser($this->randomUser());
        $post->setTopic($this->randomTopic());
        $this->repository->add($post, $flush);
    }

    private function randomUser()
    {
        return $this->users[rand(0, count($this->users) - 1)];
    }

    private function randomTopic()
    {
        return $this->topics[rand(0, count($this->topics) - 1)];
    }
}
