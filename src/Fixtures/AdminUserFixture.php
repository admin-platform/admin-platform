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
use Platform\Bundle\AdminBundle\Model\AdminUserInterface;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

/**
 * Class AdminUserFixture.
 */
class AdminUserFixture extends AbstractFixture
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
        foreach ($options['users'] as $userData) {
            /** @var AdminUserInterface $user */
            $user = $this->factory->createNew();

            $user->setEmail($userData['email']);
            $user->setPlainPassword($userData['password']);
            $user->setEnabled($userData['enabled']);
            $user->setFirstName($userData['first_name']);
            $user->setLastName($userData['last_name']);
            $user->setUsername($userData['username'] ?? $userData['email']);
            $user->setLocaleCode($userData['locale']);

            foreach ($userData['roles'] as $role) {
                $user->addRole($role);
            }

            $this->manager->persist($user);
        }

        $this->manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_admin_user';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->arrayNode('users')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->arrayPrototype()
                    ->children()
                        ->scalarNode('email')->isRequired()->cannotBeEmpty()->end()
                        ->scalarNode('password')->isRequired()->cannotBeEmpty()->end()
                        ->booleanNode('enabled')->defaultTrue()->end()
                        ->arrayNode('roles')
                            ->defaultValue([AdminUserInterface::DEFAULT_ADMIN_ROLE])
                            ->requiresAtLeastOneElement()
                            ->scalarPrototype()->cannotBeEmpty()->end()
                        ->end()
                        ->scalarNode('first_name')->defaultNull()->end()
                        ->scalarNode('last_name')->defaultNull()->end()
                        ->scalarNode('username')->defaultNull()->end()
                        ->scalarNode('locale')->defaultValue('en')->end()
        ;
    }
}
