<?php

namespace App\Command;

use App\Entity\AppConfig;
use App\Repository\AppConfigRepository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetAppConfigCommand extends Command
{
    protected static $defaultName = 'app:set-app-config';

    /**
     * @var AppConfigRepository $configRepository
     */
    private $configRepository;

    public function __construct(ContainerInterface $container, string $name = null)
    {
        parent::__construct($name);
        $this->configRepository = $container->get('doctrine')->getRepository(AppConfig::class);
    }

    protected function configure()
    {
        $this->setDescription('Set app config');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Setting Data');
        $this->setStartDate();
        return Command::SUCCESS;
    }

    private function setStartDate(): bool
    {
        return $this->configRepository->setConfigValue('startDate', (new \DateTime())->format('Y-m-d'));
    }
}
