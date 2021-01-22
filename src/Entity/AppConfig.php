<?php

namespace App\Entity;

use App\Repository\AppConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AppConfigRepository::class)
 */
class AppConfig
{

    const VALID_KEYS = [
        'key',
        'value'
    ];

    const START_DATE = 1;
    const CONFIG_KEYS = [
        self::START_DATE => 'startDate'
    ];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $configKey;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $configValue;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getConfigKey(): ?string
    {
        return $this->configKey;
    }

    public function setConfigKey(string $configKey): self
    {
        $this->configKey = $configKey;

        return $this;
    }

    public function getConfigValue(): ?string
    {
        return $this->configValue;
    }

    public function setConfigValue(string $configValue): self
    {
        $this->configValue = $configValue;

        return $this;
    }
}
