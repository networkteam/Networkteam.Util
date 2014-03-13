<?php
namespace Networkteam\Mueggenburg\Offers\Tests\Unit\Service;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use TYPO3\Flow\Tests\UnitTestCase;

class JsonResourceTypeConverterTest extends UnitTestCase {

	/**
	 * @test
	 */
	public function canConvertReturnsTrueForCorrectValues() {
		$typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
		$result = $typeConverter->canConvertFrom(array(
			'filename' => 'abc.csv',
			'value' => 'data:abc',
			'mime' => 'application/csv'
		), 'TYPO3\Flow\Resource\Resource');

		$this->assertTrue($result);
	}

	/**
	 * @test
	 */
	public function canConvertReturnsFalseForMultipartValues() {
		$typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
		$result = $typeConverter->canConvertFrom(array(
			'submittedFile' => array(
				'filename' => 'abc.csv'
			),
			'tmp_name' => 'tmp_abc'
		), 'TYPO3\Flow\Resource\Resource');

		$this->assertFalse($result);
	}

	/**
	 * @test
	 */
	public function convertFromBuildsResourceFromCorrectValues() {
		$content = 'My little pony';

		$mockResource = $this->getMock('TYPO3\Flow\Resource\Resource');
		$mockResourceManager = $this->getMock('TYPO3\Flow\Resource\ResourceManager');

		$typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
		$this->inject($typeConverter, 'resourceManager', $mockResourceManager);

		$mockResourceManager->expects($this->once())->method('createResourceFromContent')->with($content, 'file.txt')->will($this->returnValue($mockResource));

		$result = $typeConverter->convertFrom(array(
			'filename' => 'file.txt',
			'value' => 'data:text/plain;base64,' . base64_encode($content),
			'mime' => 'text/plain'
		), 'TYPO3\Flow\Resource\Resource');

		$this->assertSame($mockResource, $result);
	}

	/**
	 * @test
	 * @dataProvider
	 */
	public function convertFromBuildsReturnsErrorForInvalidValue() {
		$mockResourceManager = $this->getMock('TYPO3\Flow\Resource\ResourceManager');

		$typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
		$this->inject($typeConverter, 'resourceManager', $mockResourceManager);

		$mockResourceManager->expects($this->never())->method('createResourceFromContent');

		$result = $typeConverter->convertFrom(array(
			'filename' => 'file.txt',
			'value' => 'undefined',
			'mime' => 'text/plain'
		), 'TYPO3\Flow\Resource\Resource');

		$this->assertInstanceOf('TYPO3\Flow\Error\Error', $result);
	}

}
