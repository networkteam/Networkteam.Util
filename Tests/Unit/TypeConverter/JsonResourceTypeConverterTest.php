<?php

namespace Networkteam\Util\Tests\Unit\Service;

/***************************************************************
 *  (c) 2013 networkteam GmbH - all rights reserved
 ***************************************************************/

use Neos\Flow\Tests\UnitTestCase;

class JsonResourceTypeConverterTest extends UnitTestCase
{

    /**
     * @test
     */
    public function canConvertReturnsTrueForCorrectValues()
    {
        $typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
        $result = $typeConverter->canConvertFrom(array(
            'filename' => 'abc.csv',
            'value' => 'data:abc',
            'mime' => 'application/csv'
        ), 'Neos\Flow\ResourceManagement\PersistentResource');

        $this->assertTrue($result);
    }

    /**
     * @test
     */
    public function canConvertReturnsFalseForMultipartValues()
    {
        $typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
        $result = $typeConverter->canConvertFrom(array(
            'submittedFile' => array(
                'filename' => 'abc.csv'
            ),
            'tmp_name' => 'tmp_abc'
        ), 'Neos\Flow\ResourceManagement\PersistentResource');

        $this->assertFalse($result);
    }

    /**
     * @test
     */
    public function convertFromBuildsResourceFromCorrectValues()
    {
        $content = 'My little pony';

        $mockResource = $this->getMockBuilder('Neos\Flow\ResourceManagement\PersistentResource')->getMock();
        $mockResourceManager = $this->getMockBuilder('Neos\Flow\ResourceManagement\ResourceManager')->getMock();

        $typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
        $this->inject($typeConverter, 'resourceManager', $mockResourceManager);

        $mockResourceManager->expects($this->once())->method('importResourceFromContent')->with($content,
            'file.txt')->will($this->returnValue($mockResource));

        $result = $typeConverter->convertFrom(array(
            'filename' => 'file.txt',
            'value' => 'data:text/plain;base64,' . base64_encode($content),
            'mime' => 'text/plain'
        ), 'Neos\Flow\ResourceManagement\PersistentResource');

        $this->assertSame($mockResource, $result);
    }

    /**
     * @test
     * @dataProvider
     */
    public function convertFromBuildsReturnsErrorForInvalidValue()
    {
        $mockResourceManager = $this->getMockBuilder('Neos\Flow\ResourceManagement\ResourceManager')->getMock();

        $typeConverter = new \Networkteam\Util\TypeConverter\JsonResourceTypeConverter();
        $this->inject($typeConverter, 'resourceManager', $mockResourceManager);

        $mockResourceManager->expects($this->never())->method('importResourceFromContent');

        $result = $typeConverter->convertFrom(array(
            'filename' => 'file.txt',
            'value' => 'undefined',
            'mime' => 'text/plain'
        ), 'Neos\Flow\ResourceManagement\PersistentResource');

        $this->assertInstanceOf('Neos\Error\Messages\Error', $result);
    }
}
