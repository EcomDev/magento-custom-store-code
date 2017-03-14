<?php

/**
 * Rewritten url rewrite request model in order to set correct target path (with special store code)
 */
class EcomDev_CustomStoreCode_Model_Rewrite_Core_Url_Rewrite_Request extends Mage_Core_Model_Url_Rewrite_Request
{

    /**
     * {@inheritdoc}
     */
    protected function _processRedirectOptions()
    {
        $isPermanentRedirectOption = $this->_rewrite->hasOption('RP');

        $external = substr($this->_rewrite->getTargetPath(), 0, 6);

        if ($external === 'http:/' || $external === 'https:') {
            $destinationStoreCode = $this->_app->getStore($this->_rewrite->getStoreId())->getCode();
            $this->_setStoreCodeCookie($destinationStoreCode);
            $this->_sendRedirectHeaders($this->_rewrite->getTargetPath(), $isPermanentRedirectOption);
        }

        $targetUrl = $this->_request->getBaseUrl() . '/' . $this->_rewrite->getTargetPath();

        $specialStoreCode = $this->getSpecialStoreCode();

        if ($this->useSpecialStoreCode() && $specialStoreCode) {
            $targetUrl = $this->_request->getBaseUrl() . '/' . $specialStoreCode . '/'
                         . $this->_rewrite->getTargetPath();
        }

        if ($this->_rewrite->hasOption('R') || $isPermanentRedirectOption) {
            $this->_sendRedirectHeaders($targetUrl, $isPermanentRedirectOption);
        }

        $queryString = $this->_getQueryString();

        if ($queryString) {
            $targetUrl .= '?' . $queryString;
        }

        $this->_request->setRequestUri($targetUrl);
        $this->_request->setPathInfo($this->_rewrite->getTargetPath());

        return $this;
    }

    /**
     * Check if use special code in url
     * @return bool
     */
    protected function useSpecialStoreCode()
    {
        return Mage::getStoreConfigFlag(
            EcomDev_CustomStoreCode_Model_Rewrite_Core_Store::XML_PATH_STORE_USE_SPECIAL_CODE
        );
    }

    /**
     * Retrieve special store code
     * @return string|null
     */
    protected function getSpecialStoreCode()
    {
        return Mage::getStoreConfig(
            EcomDev_CustomStoreCode_Model_Rewrite_Core_Store::XML_PATH_STORE_SPECIAL_CODE_VALUE
        );
    }
}