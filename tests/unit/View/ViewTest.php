<?php

use PHPUnit\Framework\TestCase;
use Explt13\Nosmi\Base\View;
use Explt13\Nosmi\Interfaces\ConfigInterface;
use Explt13\Nosmi\Interfaces\LightRouteInterface;
use Explt13\Nosmi\Exceptions\FileNotFoundException;
use Explt13\Nosmi\Interfaces\ViewInterface;
use Explt13\Nosmi\Routing\Route;
use PHPUnit\Framework\MockObject\MockObject;

class ViewTest extends TestCase
{
    private ConfigInterface&MockObject $configMock;
    private LightRouteInterface&MockObject $routeMock;
    private ViewInterface $view;

    protected function setUp(): void
    {
        $this->configMock = $this->createMock(ConfigInterface::class);
        $this->routeMock = $this->createMock(LightRouteInterface::class);

        $this->configMock->method('get')->willReturnMap([
            ['INCLUDE_LAYOUT_BY_DEFAULT', false],
            ['DEFAULT_LAYOUT_FILENAME', 'default'],
            ['APP_VIEWS', '/var/www/packages/nosmi/tests/unit/View/views'],
            ['APP_LAYOUTS', '/var/www/packages/nosmi/tests/unit/View/layouts']
        ]);

        $this->view = new View($this->configMock);
    }

    public function testWithLayoutSetsLayoutFilenameAndIncludesLayout(): void
    {
        $this->view->withLayout('custom-layout');
        $this->assertSame('custom-layout', $this->getPrivateProperty($this->view, 'layout_filename'));
        $this->assertTrue($this->getPrivateProperty($this->view, 'include_layout'));
    }

    public function testWithMetaAddsMetaData(): void
    {
        $this->view->withMeta('description', 'Test description');
        $meta = $this->getPrivateProperty($this->view, 'meta');
        $this->assertArrayHasKey('description', $meta);
        $this->assertSame('Test description', $meta['description']);
    }

    public function testWithMetaArrayAddsMultipleMetaData(): void
    {
        $metaArray = ['author' => 'John Doe', 'keywords' => 'php, testing'];
        $this->view->withMetaArray($metaArray);
        $meta = $this->getPrivateProperty($this->view, 'meta');
        $this->assertSame($metaArray, $meta);
    }

    public function testWithDataAddsData(): void
    {
        $this->view->withData('key', 'value');
        $data = $this->getPrivateProperty($this->view, 'data');
        $this->assertArrayHasKey('key', $data);
        $this->assertSame('value', $data['key']);
    }

    public function testWithDataArrayAddsMultipleData(): void
    {
        $dataArray = ['key1' => 'value1', 'key2' => 'value2'];
        $this->view->withDataArray($dataArray);
        $data = $this->getPrivateProperty($this->view, 'data');
        $this->assertSame($dataArray, $data);
    }

    public function testRenderThrowsFileNotFoundExceptionForInvalidView(): void
    {
        $this->routeMock->method('getController')->willReturn('NameSpace\TestController');
        $this->view->withRoute($this->routeMock);

        $this->expectException(FileNotFoundException::class);
        $this->view->render('nonexistent-view');
    }

    public function testRenderReturnsContentWhenWithReturnIsSet(): void
    {
        $this->routeMock->method('getController')->willReturn('NameSpace\TestController');
        $this->view->withRoute($this->routeMock);
        $this->view->withReturn();

        $viewFile = '/var/www/packages/nosmi/tests/unit/View/views/TestController/test-view.php';
        file_put_contents($viewFile, '<?php echo "Test Content"; ?>');

        $output = $this->view->render('test-view');
        $this->assertSame('Test Content', $output);

        unlink($viewFile);
    }

    public function testRenderReturnsContentWhenWithEchoImmediately(): void
    {
        $this->routeMock->method('getController')->willReturn('NameSpace\SomeController');
        $this->view->withRoute($this->routeMock);
        $this->expectOutputString('there is some content' . PHP_EOL);
        $this->view->render('test-view');

    }

    public function testRenderReturnsContentWithLayout(): void
    {
        $this->routeMock->method('getController')->willReturn('NameSpace\SomeController');
        $this->view->withRoute($this->routeMock);
        $output = $this->view->withReturn()->withLayout('default_layout')->render('test-view');
        $this->assertSame('<div>
    <h1>
        Some header
    </h1>
    there is some content
    <div>
        End of the layout
    </div>
</div>',
        $output);

    }

    private function getPrivateProperty(object $object, string $property)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}