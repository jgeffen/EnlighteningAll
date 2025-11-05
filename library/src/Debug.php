<?php
	/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
	*/
	
	use JetBrains\PhpStorm\NoReturn;
	use Monolog\Logger;
	
	/**
	 * Debug
	 */
	class Debug {
		private float  $start_time;
		private string $file;
		private string $line;
		private string $origin;
		
		/**
		 * Reports data of a variable.
		 *
		 * @param mixed $variables
		 *
		 * @return Debug
		 */
		public static function ShowData(...$variables): Debug {
			$debug = debug_backtrace();
			
			foreach($variables as $key => $variable) {
				if(!is_null($variable)) {
					echo sprintf("Printing (%s) on line %s in file %s", gettype($variable), $debug[0]['line'], str_replace(dirname(__DIR__, 2), '', $debug[0]['file']));
					echo '<pre>';
					echo htmlentities(print_r($variable, TRUE));
					echo '</pre>';
					echo (isset($debug[1]['function'])) ? sprintf("This data could have originated from \"%s\"", $debug[1]['function']) : '';
					echo (isset($debug[1]['class'])) ? sprintf(" in the class \"%s\"", $debug[1]['class']) : '';
				} else {
					echo sprintf("Printing %s on line %s in file %s<br><br>", gettype($variable), $debug[0]['line'], str_replace(dirname(__DIR__, 2), '', $debug[0]['file']));
					echo 'Q: Why did the scarecrow win an award?<br>';
					echo 'A: Because he was outstanding in his field.<br><br>';
					echo 'Deryk says, "Maybe use a null value next time?"';
				}
				
				if($key + 1 < count($variables)) echo '<hr>';
			}
			
			return new Debug();
		}
		
		/**
		 * @param string      $message            The error message that should be logged.
		 * @param int         $message_type       Says where the error should go. [https://www.php.net/manual/en/function.error-log]
		 * @param string|null $destination        The destination.
		 * @param string|null $additional_headers The extra headers.
		 *
		 * @return string
		 */
		static function Error(string $message, int $message_type = 0, ?string $destination = NULL, ?string $additional_headers = NULL): string {
			$debug  = debug_backtrace();
			$caller = array_shift($debug);
			$error  = sprintf("[%s](%s): %s", str_replace(dirname(__DIR__, 2), '', $caller['file']), $caller['line'], $message);
			
			error_log($error, $message_type, $destination, $additional_headers);
			
			return $message;
		}
		
		/**
		 * @param Error|Exception $exception
		 * @param Logger|null     $logger {@link https://github.com/Seldaek/monolog View on GitHub}
		 * @param int             $level  {@link https://github.com/Seldaek/monolog/blob/main/doc/01-usage.md#log-levels Log Levels}
		 *
		 * @return string
		 */
		static function Exception(Error|Exception $exception, ?Logger $logger = NULL, int $level = Logger::ERROR): string {
			if($logger) {
				$logger->log($level, $exception->getMessage());
				$logger->log($level, $exception->getTraceAsString());
			} else {
				error_log($exception->getMessage());
				error_log($exception->getTraceAsString());
			}
			
			return $exception->getMessage();
		}
		
		/**
		 * @param string $message
		 * @param string $messageType
		 * @param bool   $arrayWalk
		 */
		public static function Output(string $message, string $messageType = '', bool $arrayWalk = FALSE): void {
			$messageType = ($arrayWalk !== FALSE) ? $arrayWalk : $messageType;
			$message     = ((strlen($message) > 150) ? chunk_split($message, 150, PHP_EOL) : $message);
			
			if(empty($message)) return;
			
			if(strstr($message, PHP_EOL)) {
				$messageLines = array_filter(explode(PHP_EOL, $message));
				
				foreach($messageLines as $messageLine) {
					self::Output($messageLine, $messageType, $arrayWalk);
				}
			} else {
				switch($messageType) {
					case 'error':
						echo "[\033[1;91m   ERROR   \033[0m] - ";
						break;
					case 'failure':
						echo "[\033[0;31m  FAILURE  \033[0m] - ";
						break;
					case 'success':
						echo "[\033[0;32m  SUCCESS  \033[0m] - ";
						break;
					case 'attention':
						echo "[\033[0;36m ATTENTION \033[0m] - ";
						break;
					default:
						/* Check for Custom */
						if(str_contains($messageType, 'custom')) {
							$array = explode('|', $messageType);
							echo $array[1];
						} else {
							echo "[     -     ] - ";
						}
						break;
				}
				
				echo $message . PHP_EOL;
			}
		}
		
		/**
		 * Star debug process.
		 *
		 * @return $this
		 */
		public function start(): Debug {
			$this->update();
			$this->start_time = microtime(TRUE);
			
			error_log(sprintf("Execution Here. Line %d in %s 0.0000s (%s)", $this->line, $this->file, $this->origin));
			
			return $this;
		}
		
		/**
		 * @return void
		 */
		private function update(): void {
			$debug = debug_backtrace();
			
			$this->file   = basename($debug[1]['file'] ?? $debug[0]['file'] ?? 'Unknown');
			$this->line   = $debug[0]['line'] ?? 0;
			$this->origin = str_replace(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT'), '', $debug[2]['file'] ?? $debug[1]['file'] ?? 'N/A');
		}
		
		/**
		 * Show debug tick.
		 */
		public function tick(): void {
			$this->update();
			error_log(sprintf("Execution Tick. Line %d in %s %ss (%s)", $this->line, $this->file, $this->time(), $this->origin));
		}
		
		/**
		 * Gets elapsed time.
		 *
		 * @return string
		 */
		private function time(): string {
			return number_format(microtime(TRUE) - $this->start_time, 4);
		}
		
		/**
		 * Stop debug process.
		 */
		public function stop(): void {
			$this->update();
			error_log(sprintf("Execution Stop. Line %d in %s %ss (%s)", $this->line, $this->file, $this->time(), $this->origin));
		}
		
		/**
		 * Exits all functionality.
		 */
		#[NoReturn] public function exit(): void {
			exit;
		}
	}