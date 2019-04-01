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

use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareContext;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class AdminContext.
 */
class AdminContext extends MinkContext implements KernelAwareContext
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel): void
    {
        $this->kernel = $kernel;
    }

    /**
     * @When /^I login in as "([^"]*)"$/
     * @Given /^I am logged in as "([^"]*)"$/
     *
     * @param string $name
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

    /**
     * @Then /^I should see "([^"]*)" in grid$/
     * @param string $text
     */
    public function iShouldSeeInGrid(string $text)
    {
        $this->assertElementContainsText('.ui.sortable.stackable.celled.table', $text);
    }

    /**
     * @Given /^I should not see "([^"]*)" in grid$/
     * @param string $text
     */
    public function iShouldNotSeeInGrid(string $text)
    {
        $this->assertElementNotContainsText('.ui.sortable.stackable.celled.table', $text);
    }

    /**
     * @Then /^I should see "([^"]*)" flash message$/
     * @param string $text
     */
    public function iShouldSeeFlashMessage(string $text)
    {
        $this->assertElementContainsText('.sylius-flash-message', $text);
    }

    /**
     * @Given /^I am on users page$/
     */
    public function iAmOnUsersPage()
    {
        $this->visit('/users');
    }

    /**
     * @Then /^I edit "([^"]*)" from grid$/
     * @param string $text
     */
    public function iEditFromGrid(string $text)
    {
        $this->getSession()->getPage()->find(
            'xpath',
            "//tr[@class=\"item\"]/td[text()[contains(., \"{$text}\")]]/../"
            . 'td/descendant::a[text()[contains(., "Edit")]]'
        )->click();
    }

    /**
     * @Given /^I change user name to "([^"]*)"$/
     * @param string $name
     */
    public function iChangeUserNameTo(string $name)
    {
        $this->fillField('Username', $name);
        $this->pressButton('Save changes');
    }
}
