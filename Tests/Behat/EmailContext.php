<?php
namespace Networkteam\Util\Tests\Behat;

/***************************************************************
 *  (c) 2014 networkteam GmbH - all rights reserved
 ***************************************************************/

use Behat\Behat\Context\BehatContext,
	Behat\Behat\Event\ScenarioEvent;
use Behat\Gherkin\Node\TableNode;
use Guzzle\Http\Client;
use PHPUnit_Framework_Assert as Assert;

class EmailContext extends BehatContext {

	/**
	 * @var Client
	 */
	protected $fakeSmtpClient;

	/**
	 * @param array $parameters
	 */
	public function __construct(array $parameters) {
		if (isset($parameters['fake_smtp_url'])) {
			$this->fakeSmtpClient = new Client($parameters['fake_smtp_url']);
		}
	}

	/**
	 * @BeforeScenario @email
	 *
	 * @param ScenarioEvent $event
	 */
	public function resetSmtpMessages(ScenarioEvent $event = NULL) {
		$this->fakeSmtpClient->post('/reset')->send();
	}

	/**
	 * @Given /^I empty all sent emails$/
	 */
	public function iEmptyAllSentEmails() {
		$this->resetSmtpMessages();
	}

	/**
	 * @Given /^I should have no emails$/
	 */
	public function iShouldHaveNoEmails() {
		$messages = $this->fakeSmtpClient->get('/messages')->send()->json();
		Assert::assertEmpty($messages, 'There should be no sent emails');
	}

	/**
	 * @Then /^I should have the following emails:$/
	 */
	public function iShouldHaveTheFollowingEmails(TableNode $table) {
		$messages = $this->fakeSmtpClient->get('/messages')->send()->json();
		Assert::assertNotEmpty($messages, 'There should be at least one email');

		$receivedMessages = array();
		foreach ($messages as $message) {
			if (preg_match('/<(.*?)>/', html_entity_decode($message['To']), $matches)) {
				$to = $matches[1];
			} else {
				$to = $message['To'];
			}
			$receivedMessages[] = array(
				'to' => $to,
				'subject' => $message['Subject']
			);
		}

		$rows = $table->getHash();
		foreach ($rows as $tableRow) {
			Assert::assertContains($tableRow, $receivedMessages);
		}
	}

	/**
	 * @param string $email
	 * @return array The email message if found
	 */
	public function findEmailByRecipient($email) {
		$messages = $this->fakeSmtpClient->get('/messages')->send()->json();
		Assert::assertNotEmpty($messages, 'There should be at least one email');
		foreach ($messages as $message) {
			if (isset($message['To']) && strpos($message['To'], $email) !== FALSE) {
				return $message;
			}
		}
		// TODO Show other emails?
		Assert::fail('No email for "' . $email . '" found');
	}

}
