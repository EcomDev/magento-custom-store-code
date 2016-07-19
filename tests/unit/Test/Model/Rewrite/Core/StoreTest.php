<?php

use EcomDev_CustomStoreCode_Model_Rewrite_Core_Store as RewrittenStore;

/**
 * @loadSharedFixture ~/scopes
 */
class EcomDev_CustomStoreCodeTest_Test_Model_Rewrite_Core_StoreTest
    extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @var RewrittenStore
     */
    protected $model;
    
    protected function setUp()
    {
        $this->model = new RewrittenStore();
    }

    /**
     * @param $storeId
     * @dataProvider dataProvider
     */
    public function testItAddsCustomCodeToUrl($storeId)
    {
        $this->model->load($storeId);
        
        $this->assertEquals(
            $this->expected('auto')->getUrl(),
            $this->model->getBaseUrl()
        );
    }
    
    public function testItHasListOfConfigNodesToResetCacheFor()
    {
        $this->assertObjectHasAttribute('_resetCachedNodes', $this->model);
        
        $this->assertAttributeEquals(
            array(
                RewrittenStore::XML_PATH_UNSECURE_BASE_URL,
                RewrittenStore::XML_PATH_UNSECURE_BASE_LINK_URL,
                'web/unsecure/base_skin_url',
                'web/unsecure/base_media_url',
                'web/unsecure/base_js_url',
                RewrittenStore::XML_PATH_SECURE_BASE_URL,
                RewrittenStore::XML_PATH_SECURE_BASE_LINK_URL,
                'web/secure/base_skin_url',
                'web/secure/base_media_url',
                'web/secure/base_js_url',
            ),
            '_resetCachedNodes',
            $this->model
        );
    }
    
    public function testItIsPossibleToAddOwnConfigPathToCacheReset()
    {
        $expectedValues = $this->readAttribute($this->model, '_resetCachedNodes');
        $expectedValues[] = 'my/custom/path';
        
        $this->model->addResetCacheNode('my/custom/path');
        
        $this->assertAttributeEquals(
            $expectedValues,
            '_resetCachedNodes',
            $this->model
        );
    }

    public function testItIsPossibleToRemoveConfigPathToCacheReset()
    {
        $expectedValues = $this->readAttribute($this->model, '_resetCachedNodes');
        $expectedValues[] = 'my/custom/path';
        $expectedValues[] = 'my/custom/path3';        

        $this->model->addResetCacheNode('my/custom/path');
        $this->model->addResetCacheNode('my/custom/path2');
        $this->model->addResetCacheNode('my/custom/path3');
        
        $this->model->removeResetCacheNode('my/custom/path2');

        $this->assertAttributeEquals(
            $expectedValues,
            '_resetCachedNodes',
            $this->model
        );
    }

    /**
     * Store data config
     * 
     * @param string $storeId
     * @param array $data
     * @dataProvider dataProvider
     */
    public function testItIsPossibleToOverrideConfigValues($storeId, $data)
    {
        $this->model->load($storeId);
        $this->model->initConfigCache();
        $this->model->updateConfig($data);
        
        foreach ($this->expected('auto')->getConfig() as $key => $value) {
            $this->assertEquals($value, $this->model->getConfig($key), 'Configuration value for: ' . $key . ' is invalid');
        }
        
        foreach ($this->expected('auto')->getUnsecureUrls() as $type => $value) {
            $this->assertEquals($value, $this->model->getBaseUrl($type), 'Unsecure ' . $type . ' url is invalid');
        }

        foreach ($this->expected('auto')->getSecureUrls() as $type => $value) {
            $this->assertEquals($value, $this->model->getBaseUrl($type, true),  'Secure ' . $type . ' url is invalid');
        }
    }
}
