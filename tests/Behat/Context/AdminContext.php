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

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\MinkExtension\Context\RawMinkContext;
use Doctrine\Persistence\ObjectManager;
use Platform\Bundle\AdminBundle\Model\AdminUserInterface;
use Sylius\Component\User\Repository\UserRepositoryInterface;
use Webmozart\Assert\Assert;

class AdminContext extends RawMinkContext
{
    private MinkContext $minkContext;

    private UserRepositoryInterface $adminUserRepository;

    private ObjectManager $userManager;

    private ?string $passwordHash;

    public function __construct(UserRepositoryInterface $adminUserRepository, ObjectManager $userManager)
    {
        $this->adminUserRepository = $adminUserRepository;
        $this->userManager = $userManager;
    }

    /** @BeforeScenario */
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        $environment = $scope->getEnvironment();

        $this->minkContext = $environment->getContext(MinkContext::class);
    }

    /**
     * @When /^I login in as "([^"]*)"$/
     * @Given /^I am logged in as "([^"]*)"$/
     */
    public function iLoginAsUser(string $name): void
    {
        try {
            $this->getSession()->setBasicAuth($name);
        } catch (UnsupportedDriverActionException $e) {
            $this->visitPath('/');
            $this->getSession()->setCookie('test_auth', $name);
        }
    }

    /** @Then /^I should see "([^"]*)" in grid$/ */
    public function iShouldSeeInGrid(string $text): void
    {
        $this->minkContext->assertElementContainsText('.ui.sortable.stackable.celled.table', $text);
    }

    /** @Given /^I should not see "([^"]*)" in grid$/ */
    public function iShouldNotSeeInGrid(string $text): void
    {
        $this->minkContext->assertElementNotContainsText('.ui.sortable.stackable.celled.table', $text);
    }

    /** @Then /^I should see "([^"]*)" flash message$/ */
    public function iShouldSeeFlashMessage(string $text): void
    {
        $this->minkContext->assertElementContainsText('.sylius-flash-message', $text);
    }

    /** @Given /^I am on users page$/ */
    public function iAmOnUsersPage(): void
    {
        $this->minkContext->visit('/users');
    }

    /** @Then /^I edit "([^"]*)" from grid$/ */
    public function iEditFromGrid(string $text): void
    {
        $this->getSession()->getPage()->find(
            'xpath',
            "//tr[@class=\"item\"]/td[text()[contains(., \"{$text}\")]]/../"
            . 'td/descendant::a[text()[contains(., "Edit")]]'
        )->click();
    }

    /** @Given /^I change user name to "([^"]*)"$/ */
    public function iChangeUserNameTo(string $name): void
    {
        $this->minkContext->fillField('Username', $name);
        $this->minkContext->pressButton('Save changes');
    }

    /** @Given /^I have written down password hash of "([^"]*)"$/ */
    public function iHaveWrittenDownPasswordHashOf(string $username): void
    {
        /** @var AdminUserInterface $user */
        $user = $this->adminUserRepository->findOneBy(['username' => $username]);
        $this->passwordHash = $user->getPassword();
    }

    /** @Given /^Password hash of "([^"]*)" should differ from hash i have written down$/ */
    public function passwordHashOfShouldDifferFromHashIHaveWrittenDown(string $username): void
    {
        Assert::notNull($this->passwordHash, 'Password hash was not stored');
        $this->userManager->clear();

        /** @var AdminUserInterface $user */
        $user = $this->adminUserRepository->findOneBy(['username' => $username]);

        Assert::notSame($user->getPassword(), $this->passwordHash);
    }
}
