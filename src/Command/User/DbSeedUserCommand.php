<?php

namespace App\Command\User;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DbSeedUserCommand extends Command
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
        parent::__construct();
    }

    protected static $defaultName = 'db:seed:user';
    protected static $defaultDescription = 'The command deletes existing users and generates new 5 users';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->deleteExistingUsers();

        $user = new User();
        $user->setRoles([]);
        $user->setPassword('$2y$13$RQ39K5I3jEZ6XU1I3TQSOuytdidd3AfP7lxuqlHWi0yfPXFMQ0PiW'); // admin123

        $emails = [
            "Piotr@wp.pl",
            "Bogdan@wp.pl",
            "Maciej@wp.pl",
            "Tomasz@wp.pl",
            "Kacper@wp.pl",
        ];

        foreach ($emails as $key => $email) {
            $flush = $key == (count($emails) - 1);
            $this->repository->add(clone $user->setEmail($email), $flush);
        }

        $io->success('Users have been generated');

        return Command::SUCCESS;
    }

    private function deleteExistingUsers()
    {
        $users = $this->repository->findAll(User::class);

        foreach ($users as $key => $user) {
            $flush = $key == (count($users) - 1);
            $this->repository->remove($user, $flush);
        }
    }
}
