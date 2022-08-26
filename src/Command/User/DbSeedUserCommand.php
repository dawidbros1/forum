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
    protected static $defaultDescription = 'The command deletes existing users and generates new 2 users';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->deleteExistingUsers();
        $this->addUser('user@wp.pl', []);
        $this->addUser('admin@wp.pl', ["ROLE_ADMIN"]);
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

    private function addUser($email, $roles)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword('$2y$13$RQ39K5I3jEZ6XU1I3TQSOuytdidd3AfP7lxuqlHWi0yfPXFMQ0PiW'); // admin123
        $user->setRoles($roles);
        $this->repository->add($user, true);
    }
}
