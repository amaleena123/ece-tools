<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MagentoCloud\Test\Unit\Process\Build;

use Magento\MagentoCloud\Filesystem\Driver\File;
use Magento\MagentoCloud\Process\Build\CopySampleData;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * @inheritdoc
 */
class CopySampleDataTest extends TestCase
{
    /**
     * @var CopySampleData
     */
    private $process;

    /**
     * @var LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $loggerMock;

    /**
     * @var File|\PHPUnit_Framework_MockObject_MockObject
     */
    private $fileMock;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->getMockForAbstractClass();
        $this->fileMock = $this->getMockBuilder(File::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->process = new CopySampleData(
            $this->loggerMock,
            $this->fileMock
        );
    }

    public function testExecute()
    {
        $this->fileMock->expects($this->once())
            ->method('isExists')
            ->with(MAGENTO_ROOT . 'vendor/magento/sample-data-media')
            ->willReturn(true);
        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with('Sample data media found. Marshalling to pub/media.');
        $this->fileMock->expects($this->once())
            ->method('copyDirectory')
            ->with(MAGENTO_ROOT . 'vendor/magento/sample-data-media', MAGENTO_ROOT . '/pub/media');

        $this->process->execute();
    }

    public function testExecuteNoSampleData()
    {
        $this->fileMock->expects($this->once())
            ->method('isExists')
            ->with(MAGENTO_ROOT . 'vendor/magento/sample-data-media')
            ->willReturn(false);
        $this->loggerMock->expects($this->once())
            ->method('info')
            ->with('Sample data media was not found. Skipping.');
        $this->fileMock->expects($this->never())
            ->method('copyDirectory');

        $this->process->execute();
    }
}
