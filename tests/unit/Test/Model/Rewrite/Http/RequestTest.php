<?php

/**
 * @loadSharedFixture ~/scopes
 */
class EcomDev_CustomStoreCodeTest_Test_Model_Rewrite_Http_RequestTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var EcomDev_CustomStoreCode_Model_Rewrite_Http_Request
     */
    protected $request;

    protected function setUp()
    {
        $this->request = new EcomDev_CustomStoreCode_Model_Rewrite_Http_Request();
    }

    /**
     * @param string $httpHost
     * @param int $httpPort
     * @param string $requestUri
     * @dataProvider dataProvider
     */
    public function testItRetrievesCorrectlyPathInfo($httpHost, $httpPort, $requestUri)
    {
        $_SERVER['HTTP_HOST'] = $httpHost;
        $_SERVER['HTTP_PORT'] = $httpPort;
        
        $this->request->setBaseUrl('/');
        $this->request->setRequestUri($requestUri);
        
        $this->assertEquals(
            $this->expected('auto')->getPathInfo(), $this->request->getPathInfo()
        );
        
        $this->assertEquals(
            $this->expected('auto')->getStoreCode(), Mage::app()->getStore()->getCode()
        );
        
        if ($this->expected('auto')->getActionCode()) {
            $this->assertEquals(
                $this->expected('auto')->getActionCode(),
                $this->request->getActionName()
            );
        }
    }
}
