<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SearchStorefront\Model\Search\RequestGenerator;

/**
 * Copy of Magento\CatalogSearch\Model\Search\RequestGenerator
 */
class GeneratorResolver
{
    /**
     * @var GeneratorInterface[]
     */
    private $generators;

    /**
     * @var GeneratorInterface
     */
    private $defaultGenerator;

    /**
     * @param GeneratorInterface   $defaultGenerator
     * @param GeneratorInterface[] $generators
     */
    public function __construct(GeneratorInterface $defaultGenerator, array $generators)
    {
        $this->defaultGenerator = $defaultGenerator;
        $this->generators = $generators;
    }

    /**
     * Provides request generator
     *
     * @param  string $type
     * @return GeneratorInterface
     * @throws \InvalidArgumentException
     */
    public function getGeneratorForType($type)
    {
        $generator = isset($this->generators[$type]) ? $this->generators[$type] : $this->defaultGenerator;
        if (!($generator instanceof GeneratorInterface)) {
            throw new \InvalidArgumentException(
                'Generator must implement ' . GeneratorInterface::class
            );
        }
        return $generator;
    }
}
