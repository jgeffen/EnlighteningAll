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
	
	use TijsVerkoyen\CssToInlineStyles\CssToInlineStyles;
	use Twig\Environment;
	use Twig\Error\LoaderError;
	use Twig\Error\RuntimeError;
	use Twig\Error\SyntaxError;
	use Twig\Extra\Intl\IntlExtension;
	use Twig\Loader\FilesystemLoader;
	
	class Template {
		private Environment      $environtment;
		private FilesystemLoader $filesystem_loader;
		
		private array  $environment_options;
		private array  $filesystem_loader_path;
		private string $template;
		private array  $context;
		
		/**
		 * @param string $template
		 * @param array  $context
		 */
		public function __construct(string $template, array $context = array()) {
			$this->setContext($context);
			$this->setTemplate($template);
		}
		
		/**
		 * @param string $template
		 * @param array  $context
		 * @param bool   $inline_styles
		 *
		 * @return string
		 */
		public static function Render(string $template, array $context = array(), bool $inline_styles = FALSE): string {
			$html = new Template($template, $context);
			
			return ($html)->renderHTML($inline_styles);
		}
		
		/**
		 * @param bool $inline_styles
		 *
		 * @return string
		 */
		public function renderHTML(bool $inline_styles = FALSE): string {
			try {
				$content = $this->getEnvirontment()->render($this->getTemplate(), $this->getContext());
				
				return $inline_styles ? (new CssToInlineStyles())->convert($content) : $content;
			} catch(LoaderError|RuntimeError|SyntaxError $exception) {
				error_log($exception->getMessage());
				
				return '';
			}
		}
		
		/**
		 * @return Environment
		 */
		public function getEnvirontment(): Environment {
			!isset($this->environtment) && $this->setEnvirontment();
			return $this->environtment;
		}
		
		/**
		 * @param Environment|null $environtment
		 */
		public function setEnvirontment(?Environment $environtment = NULL): void {
			$this->environtment = $environtment ?? new Environment($this->getFilesystemLoader(), $this->getEnvironmentOptions());
			is_null($environtment) && $this->environtment->addExtension(new IntlExtension());
		}
		
		/**
		 * @return string
		 */
		public function getTemplate(): string {
			return $this->template;
		}
		
		/**
		 * @param string $template
		 */
		public function setTemplate(string $template): void {
			$this->template = $template;
		}
		
		/**
		 * @return array
		 */
		public function getContext(): array {
			return $this->context;
		}
		
		/**
		 * @param array $context
		 */
		public function setContext(array $context): void {
			$this->context = array_merge(array(
				'website' => array(
					'alt'     => sprintf("%s Logo", SITE_NAME),
					'company' => SITE_COMPANY,
					'email'   => SITE_EMAIL,
					'link'    => Helpers::CurrentWebsite(),
					'logo'    => Helpers::CurrentWebsite('/images/layout/main-logo.png'),
					'name'    => SITE_NAME,
					'phone'   => SITE_PHONE,
					'support' => Helpers::CurrentWebsite('/contact.html')
				)
			), $context);
		}
		
		/**
		 * @param array $context
		 */
		public function addContext(array $context): void {
			$this->setContext(array_merge($this->getContext(), $context));
		}
		
		/**
		 * @return array
		 */
		public function getEnvironmentOptions(): array {
			!isset($this->environment_options) && $this->setEnvironmentOptions();
			return $this->environment_options;
		}
		
		/**
		 * @param array|null $environment_options
		 */
		public function setEnvironmentOptions(?array $environment_options = NULL): void {
			$this->environment_options = $environment_options ?? array('cache' => FALSE);
		}
		
		/**
		 * @return FilesystemLoader
		 */
		public function getFilesystemLoader(): FilesystemLoader {
			!isset($this->filesystem_loader) && $this->setFilesystemLoader();
			return $this->filesystem_loader;
		}
		
		/**
		 * @param FilesystemLoader|null $filesystem_loader
		 */
		public function setFilesystemLoader(?FilesystemLoader $filesystem_loader = NULL): void {
			$this->filesystem_loader = $filesystem_loader ?? new FilesystemLoader($this->getFilesystemLoaderPath());
		}
		
		/**
		 * @return array
		 */
		public function getFilesystemLoaderPath(): array {
			!isset($this->filesystem_loader_path) && $this->setFilesystemLoaderPath();
			return $this->filesystem_loader_path;
		}
		
		/**
		 * @param array|string $filesystem_loader_path
		 */
		public function setFilesystemLoaderPath(array|string $filesystem_loader_path = array()): void {
			$filesystem_loader_path       = is_string($filesystem_loader_path) ? array($filesystem_loader_path) : $filesystem_loader_path;
			$this->filesystem_loader_path = array_merge($filesystem_loader_path, array(sprintf("%s/templates", dirname(__DIR__))));
		}
	}