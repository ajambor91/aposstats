<?php

namespace App\Command;

use App\Entity\Apostasy;
use App\Service\Scrapper;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetApostatesDataCommand extends Command
{
    protected static $defaultName = 'get-apostates-data';
    private $container;
    private $data;

    public function __construct(ContainerInterface $container, string $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
    }

    protected function configure()
    {
        $this->setDescription('Get apostates data from http://licznikapostazji.pl');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->success('Scrapping Data');
        $this->getApostatesData();
        $this->saveToDb($this->data);
        return Command::SUCCESS;
    }

    private function getApostatesData(): void
    {
        $scrapper = new Scrapper();
        $this->data = $scrapper->getData();
    }

    private function saveToDb(array $data): void
    {
        $apostasyRepository = $this->container->get('doctrine')->getRepository(Apostasy::class);
        $apostasyRepository->saveApostasy($data);
    }
}
//const test = 'dupa';
//const test1 = 'dpua';
//const test2 = 'dupeczka';
//
//const arr = [...test];
//const arr2 = [...test1];
//const arr3 = [...test2];
//let fit;
//for (let i = 0; i < arr.length; i++) {
//    if(arr[i] !== arr2[i]){
//        fit[0] = i;
//
//    }
//}
//
//co poeta miał na myśli