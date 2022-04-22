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
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Platform\Bundle\AdminBundle\Model\AdminUser;
use Sylius\Component\Locale\Model\Locale;
use Symfony\Component\HttpKernel\KernelInterface;

use function sprintf;
use function exec;

class FixtureContext extends RawMinkContext
{
    private KernelInterface $kernel;

    private static bool $initialized = false;

    private array $references = [];

    private EntityManagerInterface $entityManager;

    public function __construct(KernelInterface $kernel, EntityManagerInterface $entityManager)
    {
        $this->kernel = $kernel;
        $this->entityManager = $entityManager;
    }

    /** @BeforeScenario */
    public function initializeDatabase(): void
    {
        if (self::$initialized) {
            return;
        }

        $this->dropAndCreateDatabase();

        self::$initialized = true;
    }

    private function dropAndCreateDatabase(): void
    {
        $connection = $this->entityManager->getConnection();
        $schema = $connection->createSchemaManager();
        $database = $connection->getDatabase();

        $schema->dropDatabase($database);
        $schema->createDatabase($database);
        $this->migrateSchema();

        $connection->close();
    }

    /** @AfterScenario */
    public function afterScenario(): void
    {
        (new ORMPurger($this->entityManager))->purge();
    }

    private function migrateSchema(): void
    {
        exec(sprintf('php "%s/bin/console" doctrine:schema:update --force --env=test', $this->kernel->getProjectDir()));
    }

    /**
     * @Given /^There is an admin user "([^"]*)"$/
     * @Given /^There is an admin user "([^"]*)" with locale "([^"]*)"$/
     */
    public function thereIsAnAdminUser(string $name, string $locale = null): AdminUser
    {
        if (isset($this->references[AdminUser::class][$name])) {
            return $this->references[AdminUser::class][$name];
        }

        $object = new AdminUser();
        $object->setUsername($name);
        $object->setEmail($name);
        $object->setPlainPassword($name);
        $object->setLocaleCode($this->thereIsALocale($locale)->getCode());
        $object->setEnabled(true);

        $this->entityManager->persist($object);
        $this->entityManager->flush();

        $this->references[AdminUser::class][$name] = $object;

        return $object;
    }

    /**
     * @Given /^There is a locale$/
     * @Given /^There is a locale "([^"]*)"$/
     */
    public function thereIsALocale(string $locale = null): Locale
    {
        if ($locale === null) {
            $locale = $this->kernel->getContainer()->getParameter('locale');
        }

        if (isset($this->references[Locale::class][$locale])) {
            return $this->references[Locale::class][$locale];
        }

        $object = new Locale();
        $object->setCode($locale);

        $this->entityManager->persist($object);
        $this->entityManager->flush();

        $this->references[Locale::class][$locale] = $object;

        return $object;
    }
}
