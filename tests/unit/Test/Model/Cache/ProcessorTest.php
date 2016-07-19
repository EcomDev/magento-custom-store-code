<?php

class EcomDev_CustomStoreCodeTest_Test_Model_Cache_ProcessorTest
    extends EcomDev_PHPUnit_Test_Case
{
    protected $originalRequest;

    /**
     * @var EcomDev_CustomStoreCode_Model_Cache_Processor
     */
    protected $model;
    
    protected function setUp()
    {
        $this->originalRequest = $this->app()->getRequest();
        $this->model = new EcomDev_CustomStoreCode_Model_Cache_Processor();
    }
    
    public function testItReplacesApplicationRequestObjectWhenExtractContentIsCalled()
    {
        $this->assertEquals('test', $this->model->extractContent('test'));
        $this->assertInstanceOf(
            'EcomDev_CustomStoreCode_Model_Rewrite_Http_Request',
            $this->app()->getRequest()
        );
    }
    
    protected function tearDown()
    {
        $this->app()->setRequest($this->originalRequest);
    }
}
