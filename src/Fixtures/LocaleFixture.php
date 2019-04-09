<?php

/*
 * @copyright C UAB NFQ Technologies
 *
 * This Software is the property of NFQ Technologies
 * and is protected by copyright law – it is NOT Freeware.
 *
 * Any unauthorized use of this software without a valid license key
 * is a violation of the license agreement and will be prosecuted by
 * civil and criminal law.
 *
 * Contact UAB NFQ Technologies:
 * E-mail: info@nfq.lt
 * http://www.nfq.lt
 */

declare(strict_types=1);

namespace App\Fixtures;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class LocaleFixture.
 */
class LocaleFixture extends AbstractFixture
{
    /**
     * @var ObjectManager
     */
    private $manager;

    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * LocaleFixture constructor.
     *
     * @param ObjectManager $manager
     * @param FactoryInterface $factory
     */
    public function __construct(ObjectManager $manager, FactoryInterface $factory)
    {
        $this->manager = $manager;
        $this->factory = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        foreach ($options['locales'] as $localeData) {
            /** @var LocaleInterface $locale */
            $locale = $this->factory->createNew();
            $locale->setCode($localeData['code']);

            $this->manager->persist($locale);
        }

        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_locale';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->arrayNode('locales')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('code')->isRequired()->cannotBeEmpty()->end();
    }
}
