<?php
namespace Networkteam\Util\Mail;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

interface MailerMessageInterface {

	/**
	 * Returns the mimetype of the message(text/html)
	 *
	 * @return string
	 */
	public function getFormat();

	/**
	 * Returns the message Body, can be either html or text
	 *
	 * @return string
	 */
	public function getBody();

	/**
	 * @return mixed
	 */
	public function getFrom();

	/**
	 * @return mixed
	 */
	public function getRecipient();

	/**
	 * @return string
	 */
	public function getSubject();
}
