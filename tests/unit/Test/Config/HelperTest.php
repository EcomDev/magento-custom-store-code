<?php

class EcomDev_CustomStoreCodeTest_Test_Config_HelperTest
    extends EcomDev_PHPUnit_Test_Case_Config
{
    public function testItHasHelperAliasDefinedForDataHelper()
    {
        $this->assertHelperAlias('ecomdev_customstorecode/data', 'EcomDev_CustomStoreCode_Helper_Data');
    }
}
