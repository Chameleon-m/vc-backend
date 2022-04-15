<?php
namespace App\Command;

use App\Repository\PeopleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PeopleCleanupCommand extends Command
{
    private PeopleRepository $peopleRepository;

    protected static $defaultName = 'app:people:cleanup';

    public function __construct(PeopleRepository $peopleRepository)
    {
        $this->peopleRepository = $peopleRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Deletes rejected and spam people from the database')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Dry run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('dry-run')) {
            $io->note('Dry mode enabled');

            $count = $this->peopleRepository->countOldRejected();
        } else {
            $count = $this->peopleRepository->deleteOldRejected();
        }

        $io->success(sprintf('Deleted "%d" old rejected/spam people.', $count));

        return self::SUCCESS;
    }
}