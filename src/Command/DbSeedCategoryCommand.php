<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// use App\Entity\Post;

class DbSeedCategoryCommand extends Command
{
    protected static $defaultName = 'db:seed:category';
    protected static $defaultDescription = 'Add a short description for your command';

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
        $this->category = new Category();
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->deletePosts();
        $this->deleteCategories();
        $this->addCategories(['ciekawostki', 'problemy', 'zgłoszenia', 'hobby']);

        $io->success('Dane zostały wygenerowane');
        return Command::SUCCESS;
    }

    private function deletePosts()
    {
        $postRepository = $this->em->getRepository(Post::class);
        $posts = $postRepository->findAll();
        $this->delete($postRepository, $posts);
    }

    private function deleteCategories()
    {
        $categoryRepository = $this->em->getRepository(Category::class);
        $categories = $categoryRepository->findAll();
        $this->delete($categoryRepository, $categories);
    }

    private function addCategories(array $names)
    {
        $repository = $this->em->getRepository(Category::class);

        foreach ($names as $key => $name) {
            $flash = ((count($names) - 1) == $key);
            $repository->add(clone $this->category->setName($name), $flash);
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
