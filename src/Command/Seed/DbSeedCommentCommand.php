<?php

namespace App\Command\Seed;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use App\Service\TextGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbSeedCommentCommand extends Command
{
    protected static $defaultName = 'db:seed:comment';
    protected static $defaultDescription = 'The command generates comments';

    public function __construct(EntityManagerInterface $entityManager, TextGenerator $textGenerator)
    {
        $this->repository = $entityManager->getRepository(Comment::class);

        $this->users = $entityManager->getRepository(User::class)->findAll();
        $this->posts = $entityManager->getRepository(Post::class)->findAll();

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

        if (empty($this->posts)) {
            $io->error('Posts does not exists. Use command "php bin/console db:seed:post limit" to generate posts.');
            return Command::SUCCESS;
        }

        $limit = $input->getArgument("limit");

        for ($i = 0; $i < $limit; $i++) {
            $this->addPost($i == ($limit - 1));
        }

        $io->success('Comments have been generated');
        return Command::SUCCESS;
    }

    private function addPost($flush)
    {
        $commnet = new Comment();
        $commnet->setText($this->textGenerator->generate(5, 20));
        $commnet->setUser($this->randomUser());
        $commnet->setPost($this->randomPost());
        $this->repository->add($commnet, $flush);
    }

    private function randomUser()
    {
        return $this->users[rand(0, count($this->users) - 1)];
    }

    private function randomPost()
    {
        return $this->posts[rand(0, count($this->posts) - 1)];
    }
}
