<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	use PHPMailer\PHPMailer\PHPMailer;
	
	class Mailer extends PHPMailer {
		private Template $template;
		private bool     $admin   = FALSE;
		private string   $bgColor = '#d8bb66';
		
		/**
		 * @param bool $exceptions Should we throw external exceptions?
		 */
		public function __construct(bool $exceptions = FALSE, string $template = 'boilerplates/email-default.twig', array $context = array()) {
			parent::__construct($exceptions);
			
			$this->template = new Template($template, $context);
			$this->isHTML();
		}
		
		/**
		 * Create a message and send it.
		 * Uses the sending method specified by $Mailer.
		 *
		 * @return bool false on error - See the ErrorInfo property for details of the error
		 * @throws Exception
		 *
		 */
		public function send(): bool {
			// Sign Outgoing Message
			$this->DKIM_domain     = 'enlighteningall.com';
			$this->DKIM_private    = sprintf("%s/dkim_key.private", dirname(__DIR__, 3));
			$this->DKIM_selector   = 'default';
			$this->DKIM_passphrase = '';
			$this->DKIM_identity   = $this->From;
			
			return parent::send();
		}
		
		/**
		 * @param string $template
		 * @param array  $context
		 *
		 * @return Mailer
		 *
		 * @throws Exception
		 */
		public function setBody(string $template, array $context = array()): Mailer {
			$this->getTemplate()->addContext(array(
				'template' => $template,
				'context'  => $context,
				'admin'    => $this->isAdmin(),
				'browser'  => array(
					'user_agent' => Helpers::FormatUserAgent(filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'), NULL, FALSE),
					'ip_address' => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
				),
				'title'    => $this->Subject,
				'body'     => array(
					'background_color' => $this->getBgColor()
				),
				'content'  => array(
					'background_color' => '#ffffff'
				),
				'footer'   => array(
					'background_color' => '#f6f6f6'
				)
			));
			
			$this->Body    = $this->getTemplate()->renderHTML(TRUE);
			$this->AltBody = strip_tags($this->Body);
			
			return $this;
		}
		
		/**
		 * @param string $subject
		 */
		public function setSubject(string $subject): void {
			$this->Subject = $subject;
		}
		
		/**
		 * @return Template
		 */
		public function getTemplate(): Template {
			return $this->template;
		}
		
		/**
		 * @return bool
		 */
		public function isAdmin(): bool {
			return $this->admin;
		}
		
		/**
		 * @param bool $admin
		 */
		public function setAdmin(bool $admin): void {
			$this->admin = $admin;
		}
		
		/**
		 * @return string
		 */
		public function getBgColor(): string {
			return $this->bgColor;
		}
		
		/**
		 * @param string $bgColor
		 */
		public function setBgColor(string $bgColor): void {
			$this->bgColor = $bgColor;
		}
	}