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

namespace App\Tests\Behat\Context;

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Platform\Bundle\AdminBundle\Model\AdminUser;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class FixtureContext.
 */
class FixtureContext extends RawMinkContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var array
     */
    private $references = [];

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function initializeDatabase(): void
    {
        static $initialized = false;
        if ($initialized) {
            return;
        }

        $doctrine = $this->kernel->getContainer()->get('doctrine');
        $this->dropAndCreateDatabase($doctrine->getConnection());

        /** @var EntityManager $manager */
        $manager = $doctrine->getManager();
        $metadata = $manager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($manager);
        $schemaTool->createSchema($metadata);

        $initialized = true;
    }

    /**
     * @AfterScenario
     */
    public function afterScenario(): void
    {
        (new ORMPurger($this->getManager()))->purge();
        $this->references = [];
    }

    /**
     * @param Connection $originalConnection
     */
    private function dropAndCreateDatabase(Connection $originalConnection): void
    {
        $params = $originalConnection->getParams();

        $dbName = $params['dbname'];
        unset($params['url'], $params['dbname']);

        $connection = DriverManager::getConnection($params);
        $schema = $connection->getSchemaManager();

        if (\in_array($dbName, $schema->listDatabases(), true)) {
            $schema->dropDatabase($dbName);
        }

        $schema->createDatabase($dbName);

        $connection->close();
    }

    /**
     * @return EntityManager
     */
    private function getManager(): EntityManager
    {
        return $this->kernel->getContainer()->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @Given /^There is an admin user "([^"]*)"$/
     * @Given /^There is an admin user "([^"]*)" with locale "([^"]*)"$/
     * @param string $name
     *
     * @param string|null $locale
     *
     * @return AdminUser
     */
    public function thereIsAnAdminUser(string $name, string $locale = null): AdminUser
    {
        if (isset($this->references[AdminUser::class][$name])) {
            return $this->references[AdminUser::class][$name];
        }

        $object = new AdminUser();
        $object->setUsername($name);
        $object->setEmail($name . '@example.com');
        $object->setPlainPassword($name);
        $object->setLocaleCode($this->thereIsALocale($locale)->getCode());
        $object->setEnabled(true);

        $manager = $this->getManager();
        $manager->persist($object);
        $manager->flush();

        $this->references[AdminUser::class][$name] = $object;

        return $object;
    }

    /**
     * @Given /^There is a locale$/
     * @Given /^There is a locale "([^"]*)"$/
     *
     * @param string|null $locale
     *
     * @return Locale
     */
    public function thereIsALocale(string $locale = null): Locale
    {
        if (null === $locale) {
            $locale = $this->kernel->getContainer()->getParameter('locale');
        }

        if (isset($this->references[Locale::class][$locale])) {
            return $this->references[Locale::class][$locale];
        }

        $object = new Locale();
        $object->setCode($locale);

        $manager = $this->getManager();
        $manager->persist($object);
        $manager->flush();

        $this->references[Locale::class][$locale] = $object;

        return $object;
    }
}
