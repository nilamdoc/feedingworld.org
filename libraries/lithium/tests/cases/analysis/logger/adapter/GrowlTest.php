<?php
/**
 * li₃: the most RAD framework for PHP (http://li3.me)
 *
 * Copyright 2010, Union of RAD. All rights reserved. This source
 * code is distributed under the terms of the BSD 3-Clause License.
 * The full license text can be found in the LICENSE.txt file.
 */

namespace lithium\tests\cases\analysis\logger\adapter;

use lithium\analysis\logger\adapter\Growl;

class GrowlTest extends \lithium\test\Unit {

	protected $_backup = [];

	public function setUp() {
		$this->_backup['error_reporting'] = error_reporting();
		error_reporting(E_ALL);
	}

	public function tearDown() {
		error_reporting($this->_backup['error_reporting']);
	}

	public function testGrowlWrite() {
		$connection = fopen('php://memory', 'w+');

		$growl = new Growl(compact('connection') + [
			'name' => 'Lithium',
			'title' => 'Lithium log'
		]);
		$writer = $growl->write('info', 'info: Test message.', []);
		$params = ['message' => 'info: Test message.', 'options' => []];
		$result = $writer($params, null);

		$bytes = [
			1, 0, 0, 7, 2, 2, 76, 105, 116, 104, 105, 117, 109, 0, 6, 69, 114, 114, 111, 114, 115,
			0, 8, 77, 101, 115, 115, 97, 103, 101, 115, 0, 1, 126, 154, 165, 127, 162, 58, 0, 172,
			243, 11, 201, 119, 62, 33, 133, 55, 1, 1, 0, 0, 0, 8, 0, 11, 0, 19, 0, 7, 77, 101, 115,
			115, 97, 103, 101, 115, 76, 105, 116, 104, 105, 117, 109, 32, 108, 111, 103, 105, 110,
			102, 111, 58, 32, 84, 101, 115, 116, 32, 109, 101, 115, 115, 97, 103, 101, 46, 76, 105,
			116, 104, 105, 117, 109, 213, 182, 8, 47, 80, 71, 225, 173, 12, 228, 108, 152, 140, 126,
			102, 14
		];

		rewind($connection);
		$result = array_map('ord', str_split(stream_get_contents($connection)));
		$this->assertEqual($bytes, $result);
	}

	public function testInvalidConnection() {
		$growl = new Growl([
			'name' => 'Lithium',
			'title' => 'Lithium log',
			'port' => 0
		]);
		$this->assertException('/Failed to parse address/', function() use ($growl) {
			$message = 'info: Test message.';
			$params = compact('message') + ['priority' => 'info', 'options' => []];

			$writer = $growl->write('info', $message, []);
			$writer($params, null);
		});
	}

	public function testInvalidConnectionWithForcedRegistration() {
		$growl = new Growl([
			'name' => 'Lithium',
			'title' => 'Lithium log',
			'port' => 0,
			'registered' => true
		]);
		$this->assertException('/Failed to parse address/', function() use ($growl) {
			$message = 'info: Test message.';
			$params = compact('message') + ['priority' => 'info', 'options' => []];

			$writer = $growl->write('info', $message, []);
			$writer($params, null);
		});
	}

	public function testStickyMessages() {
		$connection = fopen('php://memory', 'w+');

		$growl = new Growl(compact('connection') + [
			'name' => 'Lithium',
			'title' => 'Lithium log'
		]);
		$writer = $growl->write('info', 'info: Test message.', []);
		$params = ['message' => 'info: Test message.', 'options' => ['sticky' => true]];
		$result = $writer($params, null);

		$bytes = [
			1, 0, 0, 7, 2, 2, 76, 105, 116, 104, 105, 117, 109, 0, 6, 69, 114, 114, 111, 114, 115,
			0, 8, 77, 101, 115, 115, 97, 103, 101, 115, 0, 1, 126, 154, 165, 127, 162, 58, 0, 172,
			243, 11, 201, 119, 62, 33, 133, 55, 1, 1, 1, 0, 0, 8, 0, 11, 0, 19, 0, 7, 77, 101, 115,
			115, 97, 103, 101, 115, 76, 105, 116, 104, 105, 117, 109, 32, 108, 111, 103, 105, 110,
			102, 111, 58, 32, 84, 101, 115, 116, 32, 109, 101, 115, 115, 97, 103, 101, 46, 76, 105,
			116, 104, 105, 117, 109, 123, 79, 163, 67, 106, 115, 6, 31, 170, 247, 50, 98, 144, 44,
			105, 89
		];

		rewind($connection);
		$result = array_map('ord', str_split(stream_get_contents($connection)));
		$this->assertEqual($bytes, $result);
	}

	public function testMessagePriority() {
		$connection = fopen('php://memory', 'w+');

		$growl = new Growl(compact('connection') + [
			'name' => 'Lithium',
			'title' => 'Lithium log'
		]);
		$writer = $growl->write('info', 'info: Test message.', []);
		$params = ['message' => 'info: Test message.', 'options' => [
			'priority' => 'emergency'
		]];
		$result = $writer($params, null);

		$bytes = [
			1, 0, 0, 7, 2, 2, 76, 105, 116, 104, 105, 117, 109, 0, 6, 69, 114, 114, 111, 114, 115,
			0, 8, 77, 101, 115, 115, 97, 103, 101, 115, 0, 1, 126, 154, 165, 127, 162, 58, 0, 172,
			243, 11, 201, 119, 62, 33, 133, 55, 1, 1, 0, 4, 0, 8, 0, 11, 0, 19, 0, 7, 77, 101, 115,
			115, 97, 103, 101, 115, 76, 105, 116, 104, 105, 117, 109, 32, 108, 111, 103, 105, 110,
			102, 111, 58, 32, 84, 101, 115, 116, 32, 109, 101, 115, 115, 97, 103, 101, 46, 76, 105,
			116, 104, 105, 117, 109, 180, 219, 185, 111, 150, 248, 170, 144, 208, 88, 63, 48, 171,
			130, 209, 32
		];

		rewind($connection);
		$result = array_map('ord', str_split(stream_get_contents($connection)));
		$this->assertEqual($bytes, $result);
	}
}

?>