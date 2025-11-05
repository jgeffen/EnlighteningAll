<?php
	/*
	Copyright (c) 2021, 2022 Daerik.com
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
	use Items\Members\Posts\Comments;
	
	try {
		// Set Response
		$json_response = array(
			'status'  => 'success',
			'message' => 'DataTables loaded successfully.',
			'data'    => array_map(fn(Comments\Report $item) => array(
				'status'      => $item->getStatus()?->getValue(),
				'type'        => $item->getType()?->getValue(),
				'id'          => $item->getId(),
				'comment_id'  => $item->getCommentId(),
				'post_id'     => $item->getComment()?->getPostId(),
				'comment_by'  => $item->getComment()?->getMember()?->getUsername(),
				'reported_by' => $item->getMember()?->getUsername(),
				'timestamp'   => $item->getLastTimestamp()->format('F j, Y, g:ia'),
				'item'        => $item->toArray(),
				'options'     => Render::GetTemplate('admin/items/member-reports/comments/options.twig', array(
					'id' => $item->getId()
				))
			), Comments\Report::FetchAll(Database::Action("SELECT * FROM `member_post_comment_reports` ORDER BY `timestamp`")))
		);
	} catch(Error|PDOException $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => Debug::Exception($exception),
			'data'    => array()
		);
	} catch(Exception $exception) {
		// Set Response
		$json_response = array(
			'status'  => 'error',
			'message' => $exception->getMessage(),
			'data'    => array()
		);
	}
	
	// Output Response
	echo json_encode($json_response);