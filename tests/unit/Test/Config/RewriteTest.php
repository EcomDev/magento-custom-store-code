<?php

class EcomDev_CustomStoreCodeTest_Test_Config_RewriteTest
    extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testItRewriteCoreStoreModel()
    {
        $this->assertModelAlias(
            'core/store',
            'EcomDev_CustomStoreCode_Model_Rewrite_Core_Store'
        );
    }
}
