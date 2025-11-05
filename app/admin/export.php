<?php
	/*
	Copyright (c) 2023 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	// Imports
	use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Worksheet\Table;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	
	// Set Type
	$type = filter_input(INPUT_GET, 'type');
	
	try {
		// Switch Type
		switch($type) {
			case 'feedback-survey':
				// Variable Defaults
				$spreadsheet = new Spreadsheet();
				$sheet       = $spreadsheet->getActiveSheet();
				$items       = Items\Forms\FeedbackSurvey::FetchAll(Database::Action("SELECT * FROM `forms` WHERE `type` = 'feedback-survey' AND DATE(`timestamp`) >= :date_from AND DATE(`timestamp`) <= :date_to", array(
					'date_from' => filter_input(INPUT_GET, 'date_from'),
					'date_to'   => filter_input(INPUT_GET, 'date_to')
				)));
				
				// Define Headers
				$headers = array(
					'contact_name'                       => 'Contact Name',
					'contact_email'                      => 'Contact Email',
					'contact_phone'                      => 'Contact Phone',
					'contact_comments'                   => 'Contact Comments',
					'rating_bar_service'                 => 'Rating: Bar Service',
					'rating_bar_service_comments'        => 'Rating: Bar Service Comments',
					'rating_check_in_process'            => 'Rating: Check-In Process',
					'rating_check_in_process_comments'   => 'Rating: Check-In Process Comments',
					'rating_clean_room_arrival'          => 'Rating: Clean Room Upon Arrival',
					'rating_clean_room_arrival_comments' => 'Rating: Clean Room Upon Arrival Comments',
					'rating_food'                        => 'Rating: Food',
					'rating_food_comments'               => 'Rating: Food Comments',
					'rating_likely_to_return'            => 'Rating: Likely to Return',
					'rating_likely_to_return_comments'   => 'Rating: Likely to Return Comments',
					'rating_room_amentities'             => 'Rating: Room Amenities',
					'rating_room_amentities_comments'    => 'Rating: Room Amenities Comments',
					'rating_staff_members'               => 'Rating: Staff Members',
					'rating_staff_members_comments'      => 'Rating: Staff Members Comments',
					'comments'                           => 'Comments'
				);
				
				// Insert Headers into Spreadsheet
				$columnIndex = 1;
				foreach($headers as $header) {
					$columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
					$sheet->setCellValue($columnLetter . '1', $header);
					$columnIndex++;
				}
				
				// Insert Data into Spreadsheet
				$rowIndex = 2; // Start at row 2
				foreach($items as $item) {
					$columnIndex = 1; // Reset column index for each row
					foreach($headers as $fieldName => $header) {
						$columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
						$sheet->setCellValue($columnLetter . $rowIndex, $item->getEncoded($fieldName));
						$columnIndex++;
					}
					$rowIndex++;
				}
				
				// Define the table area (e.g., A1:C10)
				$columnStart = Coordinate::stringFromColumnIndex(1);
				$columnEnd   = Coordinate::stringFromColumnIndex(count($headers));
				
				// Add a new table to the worksheet and configure
				$table = new Table();
				$table->setName('MyTableName');
				$table->setRange($columnStart . '1:' . $columnEnd . ($rowIndex - 1));
				
				// Add the table to the worksheet
				$sheet->addTable($table);
				
				// Auto size columns
				$columnIndex = 1;
				foreach($headers as $ignored) {
					$columnLetter = Coordinate::stringFromColumnIndex($columnIndex);
					$sheet->getColumnDimension($columnLetter)->setAutoSize(TRUE);
					$columnIndex++;
				}
				
				// Output to Browser
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header(sprintf(
					"Content-Disposition: attachment;filename=\"feedback-survey-%s-to-%s.xlsx\"",
					filter_input(INPUT_GET, 'date_from'),
					filter_input(INPUT_GET, 'date_to')
				));
				
				$writer = new Xlsx($spreadsheet);
				$writer->save('php://output');
				break;
			default:
				throw new Exception(sprintf("Unknown Type: %s", $type ?? 'NULL'));
		}
	} catch(Exception $exception) {
		echo $exception->getMessage();
	}