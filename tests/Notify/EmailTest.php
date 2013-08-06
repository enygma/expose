<?php

namespace Expose\Notify;

class EmailTest extends \PHPUnit_Framework_TestCase
{
	private $email = null;

	public function setUp()
	{
		$this->email = new \Expose\Notify\Email();
	}

	/**
	 * Test the get/set of the To address
	 * 
	 * @covers \Expose\Notify\Email::setToAddress
	 * @covers \Expose\Notify\Email::getToAddress
	 */
	public function testSetToAddress()
	{
		$email = 'me@me.com';
		$this->email->setToAddress($email);

		$this->assertEquals(
			$email,
			$this->email->getToAddress()
		);
	}

	/**
	 * Try to set an invalid email To address
	 * 
	 * @covers \Expose\Notify\Email::setToAddress
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetInvalidEmailToAddress()
	{
		$email = 'invalidemail';
		$this->email->setToAddress($email);	
	}

	/**
	 * Test the get/set of the From address
	 * 
	 * @covers \Expose\Notify\Email::setFromAddress
	 * @covers \Expose\Notify\Email::getFromAddress
	 */
	public function testSetFromAddress()
	{
		$email = 'me@me.com';
		$this->email->setFromAddress($email);

		$this->assertEquals(
			$email,
			$this->email->getFromAddress()
		);
	}

	/**
	 * Try to set an invalid email From address
	 * 
	 * @covers \Expose\Notify\Email::setFromAddress
	 * @expectedException \InvalidArgumentException
	 */
	public function testSetInvalidEmailFromAddress()
	{
		$email = 'invalidemail';
		$this->email->setFromAddress($email);	
	}
}