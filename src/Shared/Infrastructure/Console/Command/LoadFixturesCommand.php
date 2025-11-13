<?php

declare(strict_types=1);

namespace Udemy\Shared\Infrastructure\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Udemy\Shared\Application\Service\DataLoader;

#[AsCommand(
	name: 'app:load-fixtures',
	description: 'Load test data (roles and users) into the database'
)]
final class LoadFixturesCommand extends Command
{
	public function __construct(
		private readonly DataLoader $dataLoader
	) {
		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->addOption('roles-only', null, InputOption::VALUE_NONE, 'Load only roles')
			->addOption('users-only', null, InputOption::VALUE_NONE, 'Load only users')
			->setHelp('This command loads test data into the database. By default, it loads both roles and users.');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$io = new SymfonyStyle($input, $output);
		$rolesOnly = $input->getOption('roles-only');
		$usersOnly = $input->getOption('users-only');

		if ($rolesOnly && $usersOnly) {
			$io->error('Cannot use --roles-only and --users-only together');
			return Command::FAILURE;
		}

		try {
			if ($rolesOnly) {
				$io->info('Loading roles...');
				$this->dataLoader->loadRoles();
				$io->success('Roles loaded successfully!');
			} elseif ($usersOnly) {
				$io->info('Loading test user...');
				$this->dataLoader->loadTestUser();
				$io->success('Test user loaded successfully!');
			} else {
				$io->info('Loading fixtures (roles and users)...');
				$this->dataLoader->loadAll();
				$io->success('Fixtures loaded successfully!');
			}

			return Command::SUCCESS;
		} catch (\Exception $e) {
			$io->error('Error loading fixtures: ' . $e->getMessage());
			return Command::FAILURE;
		}
	}
}

