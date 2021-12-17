<?php

use Behat\Step\When;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\MinkContext;
use Symfony\Component\DomCrawler\Crawler;

/**
 * features context.
 */
class FeatureContext extends MinkContext
{
    private Crawler $response;

    /**
     * @When /^I send a ([^"]*) request to "([^"]*)" with:$/
     */
    public function iSendARequestToWith($type, $uri, TableNode $post)
    {
        $fields = array();
        foreach ($post->getRowsHash() as $key => $val) {
            $fields[$key] = $val;
        }

        $driver = $this->getSession()->getDriver();
        $client = $driver->getClient();
        $this->response = $client->request($type, $uri, $fields);
    }

    /**
     * @When /^I send a ([^"]*) request to "([^"]*)"$/
     */
    public function iSendAGetRequestTo($type, $uri)
    {
        return array(
            new When(sprintf('I send a %s request to "%s" with:', $type, $uri)),
        );
    }
}
