<?php

namespace App\Command;

use App\Entity\Apostasy;
use App\Entity\City;
use App\Entity\Voivodeship;
use App\Repository\ApostasyRepository;
use App\Repository\CityRepository;
use App\Repository\VoivodeshipRepository;
use App\Service\FitCities;
use App\Service\Scrapper;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetApostatesDataCommand extends Command
{
    protected static $defaultName = 'get-apostates-data';
    /**
     * @var ApostasyRepository $apostasyRepository
     */
    private $apostasyRepository;
    /**
     * @var CityRepository $cityRepostory
     */
    private $cityRepostory;
    /**
     * @var VoivodeshipRepository $voivodeshipsRepository
     */
    private $voivodeshipsRepository;

    private $scrapper;
    /**
     * @var FitCities $fitCity
     */
    private $fitCity;

    private $terytCities;
    private $data;


    public function __construct(ContainerInterface $container,
                                Scrapper $scrapper,
                                FitCities  $fitCities,
                                string $name = null)
    {
        parent::__construct($name);
        $this->apostasyRepository = $container->get('doctrine')->getRepository(Apostasy::class);
        $this->cityRepostory = $container->get('doctrine')->getRepository(City::class);
        $this->voivodeshipsRepository = $container->get('doctrine')->getRepository(Voivodeship::class);
        $this->fitCity = $fitCities;
        $this->scrapper = $scrapper;
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
        $this->getTerytCities();
        $this->fitCity();
        $this->saveToDb();
        return Command::SUCCESS;
    }

    private function getApostatesData(): void
    {
        $scrapper = new Scrapper();
        $this->data = $scrapper->getData();
    }

    private function getTerytCities(): void
    {
        $this->terytCities = $this->voivodeshipsRepository->findAll();
    }

    private function saveToDb(): void
    {
        $this->apostasyRepository->saveApostasy($this->data);
    }

    private function getFitCity(string $city): City
    {
        return $this->cityRepostory->findOneBy(['name'=>$city]);
    }

    private function getFitVoivodeship(City $city): Voivodeship
    {
        return $this->voivodeshipsRepository->find($city->getVoivodeship()->getId());
    }

    private function fitCity(): void {
        $this->fitCity->setCities($this->terytCities);
        foreach ($this->data as $key => $datum) {
            $this->fitCity->setScrappedCity($datum['city']);
            $fittedCity = $this->getFitCity($this->fitCity->fitCities());
            $this->data[$key]['fittedCity'] = $fittedCity ?: null;
            $this->data[$key]['fittedVoivodeship'] = $this->getFitVoivodeship($fittedCity) ?: null;
        }
    }
}
