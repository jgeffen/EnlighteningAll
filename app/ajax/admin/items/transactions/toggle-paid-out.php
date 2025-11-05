<?php
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Admin\User        $admin
	 */
	
	$id       = intval($_POST['id'] ?? 0);
	$paid_out = intval($_POST['paid_out'] ?? 0);
	
	if ($id > 0) {
		try {
			Database::ArrayUpdate(
				'transactions',
				array('paid_out' => $paid_out),
				array('id' => $id)
			);
			
			echo json_encode(array(
				'status'  => 'success',
				'message' => sprintf('Transaction #%d marked as %s', $id, $paid_out ? 'paid out' : 'unpaid')
			));
		} catch (Exception $e) {
			echo json_encode(array(
				'status'  => 'error',
				'message' => $e->getMessage()
			));
		}
	} else {
		echo json_encode(array(
			'status'  => 'error',
			'message' => 'Invalid transaction ID'
		));
	}
