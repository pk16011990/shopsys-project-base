<?php

namespace SS6\ShopBundle\Model\Domain\Config;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Parser;

class DomainsConfigLoader {

	/**
	 * @var \Symfony\Component\Filesystem\Filesystem
	 */
	private $filesystem;
	/**
	 * @param \Symfony\Component\Filesystem\Filesystem $filesystem
	 */
	public function __construct(Filesystem $filesystem) {
		$this->filesystem = $filesystem;
	}

	/**
	 * @param string $filename
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	public function loadDomainConfigsFromYaml($filename) {
		$yamlParser = new Parser();

		if (!$this->filesystem->exists($filename)) {
			throw new \Symfony\Component\Filesystem\Exception\FileNotFoundException(
				'File ' . $filename . ' does not exist'
			);
		}

		$imageConfigDefinition = new DomainsConfigDefinition();
		$processor = new Processor();

		$parsedConfig = $yamlParser->parse(file_get_contents($filename));
		$processedConfig = $processor->processConfiguration($imageConfigDefinition, array($parsedConfig));

		$domainConfigs = $this->loadDomainConfigsFromArray($processedConfig);
		
		return $domainConfigs;
	}

	/**
	 * @param array $processedConfig
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig[]
	 */
	private function loadDomainConfigsFromArray($processedConfig) {
		$domainConfigs = array();

		foreach ($processedConfig[DomainsConfigDefinition::CONFIG_DOMAINS] as $domainConfigArray) {
			$domainConfigs[] = $this->processDomainConfigArray($domainConfigArray);
		}

		return $domainConfigs;
	}

	/**
	 * @param array $domainConfig
	 * @return \SS6\ShopBundle\Model\Domain\Config\DomainConfig
	 */
	private function processDomainConfigArray(array $domainConfig) {
		return new DomainConfig(
			$domainConfig[DomainsConfigDefinition::CONFIG_ID],
			$domainConfig[DomainsConfigDefinition::CONFIG_DOMAIN],
			$domainConfig[DomainsConfigDefinition::CONFIG_TEMPLATES_DIRECTORY]
		);
	}

}