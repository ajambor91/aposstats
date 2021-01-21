<?php

namespace App\Command;

use App\Repository\VoivodeshipRepository;
use App\Service\MergeTerytData;
use App\Service\Teryt;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetTerytDataCommand extends Command
{
    protected static $defaultName = 'get-teryt-data';

    private $teryt;
    private $mergeTeryt;
    private $voivodeshipRepository;

    public function __construct(Teryt $teryt, MergeTerytData $mergeTerytData, VoivodeshipRepository  $voivodeshipRepository, string $name = null)
    {
        parent::__construct($name);
        $this->teryt = $teryt;
        $this->mergeTeryt = $mergeTerytData;
        $this->voivodeshipRepository = $voivodeshipRepository;
    }

    protected function configure()
    {
        $this->setDescription('Download Teryt data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->success('Downloading Teryt data');
        $data = $this->teryt->getTerytData();
        $data = $this->mergeTeryt->mergeData($data);
        if($this->voivodeshipRepository->insertData($data)) {
            return Command::SUCCESS;
        }
        return Command::FAILURE;
    }

}
