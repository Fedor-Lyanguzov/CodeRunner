<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Behat extensions for coderunner..
 * @package    qtype
 * @subpackage coderunner
 * @copyright  2015 Richard Lobb, University of Canterbury
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


use Behat\Behat\Context\Step\Given as Given,
    Behat\Gherkin\Node\TableNode as TableNode,
    Behat\Mink\Exception\ExpectationException as ExpectationException,
    Behat\Mink\Exception\DriverException as DriverException,
    Behat\Mink\Exception\ElementNotFoundException as ElementNotFoundException;

class behat_coderunner extends behat_base {
    /**
     * Checks that a given string appears within a visible ins or del element
     * that has a background-color attribute that is not 'inherit'.
     * Intended for use only when checking the behaviour of the
     * 'Show differences' button.
     *
     * @Then /^I should see highlighted "(?P<expected>(?:[^"]|\\")*)"$/
     * @throws ExpectationException
     * @param string $expected The string that we expect to find
     */
    public function i_should_see_highlighted($expected) {
       $insxpath = "//ins[contains(@style, 'background-color') and not(contains(@style, 'background-color: inherit')) and not(contains(@style, 'display: none')) and contains(text(), '{$expected}')]";
       $delxpath = "//del[contains(@style, 'background-color') and not(contains(@style, 'background-color: inherit')) and not(contains(@style, 'display: none')) and contains(text(), '{$expected}')]";
       $msg = "'{$expected}' not found within a highlighted del or ins element";
       $driver = $this->getSession()->getDriver();
       if (!$driver->find($insxpath) && ! $driver->find($delxpath)) {
           throw new ExpectationException($msg, $this->getSession());
       }
    }

    /**
     * Checks that a given string does not appear within a visible ins or del element
     * that has a background-color attribute that is not 'inherit'.
     * Intended for use only when checking the behaviour of the
     * 'Show differences' button.
     *
     * @Then /^I should not see highlighted "(?P<nonexpected_string>(?:[^"]|\\")*)"$/
     */
    public function i_should_not_see_highlighted($nonexpected) {
        try {
            $this->i_should_see_highlighted($nonexpected);
        } catch (ExpectationException $ex) {
            return;
        }
        $msg = "'{$nonexpected}' found within a highlighted del or ins element";
        throw new ExpectationException($msg, $this->getSession());
    }
    
     /**
      * Step to ok any confirm dialogs
      * 
      * @When /^I ok any confirm dialogs/
      */
    public function i_ok_any_confirm_dialogs()
    {
       $javascript = "window.confirm = function() { return true};"  ; 
       $this->getSession()->executeScript($javascript);
    }
    
     /**
      * Step to cancel any confirm dialogs
      * 
      * @When /^I cancel any confirm dialogs/
      */
    public function i_cancel_any_confirm_dialogs()
    {
       $javascript = "window.confirm = function() { return false};"  ; 
       $this->getSession()->executeScript($javascript);
    }
    
    /**
      * Step to set a global variable 'behattesting' to true to prevent
      * textarea autoindent, which messes up behat's setting of the textarea
      * value. [See module.js]
      * 
      * @When /^I set behat testing/
      */
    public function i_set_behat_testing()
    {
        $javascript = "window.behattesting = true;"  ; 
        $this->getSession()->executeScript($javascript);
    }
    
     /**
      * Step to wait one second. [Grrr. This is messy.]
      * 
      * @When /^I wait one second/
      */
    public function i_wait_one_second()
    {
       sleep(1);
    }
    
    /**
     * Sets the contents of a field with multi-line input.
     *
     * @Given /^I set the field "(?P<field_string>(?:[^"]|\\")*)" to:$/
     * 
     * From https://moodle.org/mod/forum/discuss.php?d=283216
     */
    public function i_set_the_field_to_pystring($fieldlocator, Behat\Gherkin\Node\PyStringNode $value) {
        return array(new Given('I set the field "'. $fieldlocator. '" to "'. $value. '"'));
        //$field = behat_field_manager::get_form_field_from_label($fieldlocator, $this);
        //$string = str_replace("\n", '\\n', $value->__toString());
        //$field->set_value($string);
    }
    
     
    /**
     * @Given /^I dismiss the dialog$/
     */
    public function i_dismiss_the_dialog() {
        $this->getSession()->getDriver()->getWebDriverSession()->dismiss_alert();
        $this->handleAjaxTimeout();
    }
 }

