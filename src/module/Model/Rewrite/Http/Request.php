<?php

use EcomDev_CustomStoreCode_Model_Rewrite_Core_Store as Store;

class EcomDev_CustomStoreCode_Model_Rewrite_Http_Request
    extends Mage_Core_Controller_Request_Http
{
    /**
     * Maps of store base url to store code
     * 
     * @var string[][]
     */
    protected $_baseUrlToStore;

    /**
     * Is Secure flag override for a request
     *
     * @var bool|null
     */
    protected $_isSecureFlag;
    
    /**
     * Set the PATH_INFO string
     * Set the ORIGINAL_PATH_INFO string
     *
     * @param string|null $pathInfo
     * @return Zend_Controller_Request_Http
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $requestUri = $this->getRequestUri();
            if (null === $requestUri) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            $pos = strpos($requestUri, '?');
            if ($pos) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            $baseUrl = $this->getBaseUrl();
            $pathInfo = substr($requestUri, strlen($baseUrl));

            if ((null !== $baseUrl) && (false === $pathInfo)) {
                $pathInfo = '';
            } elseif (null === $baseUrl) {
                $pathInfo = $requestUri;
            }


            $fullBaseUrl = ($this->getServer('HTTPS', false) === 'on' ? 'https://' : 'http://' )
                . $this->getHttpHost() . '/' . $baseUrl;
            
            $fullBaseUrlToStore = $this->_getBaseUrlToStore();
            if ($fullBaseUrlToStore && isset($fullBaseUrlToStore[$fullBaseUrl])) {
                if (is_array($fullBaseUrlToStore[$fullBaseUrl])) {
                    $pathParts = explode('/', ltrim($pathInfo, '/'), 2);
                    $code = $pathParts[0];
                    if (!$this->isDirectAccessFrontendName($code)) {
                        if ($code !== '' && isset($fullBaseUrlToStore[$fullBaseUrl][$code])) {
                            $storeCode = $fullBaseUrlToStore[$fullBaseUrl][$code];
                            $pathInfo = '/'.(isset($pathParts[1]) ? $pathParts[1] : '');
                        } elseif (isset($fullBaseUrlToStore[$fullBaseUrl][''])) {
                            $storeCode = $fullBaseUrlToStore[$fullBaseUrl][''];
                            if ($code !== '') {
                                $this->setActionName('noRoute');
                            }
                        } else {
                            $this->setActionName('noRoute');
                        }
                    }
                } else {
                    $storeCode = $fullBaseUrlToStore[$fullBaseUrl];
                }
                
                if (isset($storeCode)) {
                    Mage::app()->setCurrentStore($storeCode);
                }
            }
            
            $this->_originalPathInfo = (string) $pathInfo;
            $this->_requestString = $pathInfo . ($pos!==false ? substr($requestUri, $pos) : '');
        }

        $this->_pathInfo = (string) $pathInfo;
        return $this;
    }

    /**
     * Returns match of store url to base url
     * 
     * @return array
     */
    protected function _getBaseUrlToStore()
    {
        if ($this->_baseUrlToStore === null) {
            $this->_baseUrlToStore = array();
            
            if (!Mage::isInstalled()) {
                return $this->_baseUrlToStore;
            }
            
            $stores = Mage::app()->getStores();
            /* @var $stores Store */
            foreach ($stores as $store) {
                if ($store->getIsActive()) {
                    $allowedValues = [
                        $store->getConfig(Store::XML_PATH_UNSECURE_BASE_URL),
                        $store->getConfig(Store::XML_PATH_UNSECURE_ALTERNATIVE_BASE_URL),
                        $store->getConfig(Store::XML_PATH_SECURE_BASE_URL)
                    ];

                    $specialCode = $store->getConfig(Store::XML_PATH_STORE_SPECIAL_CODE_VALUE);

                    foreach ($allowedValues as $baseUrl) {
                        if (trim($baseUrl) === '') {
                            continue;
                        }

                        if (!$store->getConfig(Store::XML_PATH_STORE_USE_SPECIAL_CODE)) {
                            if (!isset($this->_baseUrlToStore[$baseUrl])) {
                                $this->_baseUrlToStore[$baseUrl] = $store->getCode();
                            }
                        } else {
                            if (isset($this->_baseUrlToStore[$baseUrl])
                                && !is_array($this->_baseUrlToStore[$baseUrl])) {
                                $this->_baseUrlToStore[$baseUrl] = array(
                                    '' => $this->_baseUrlToStore[$baseUrl]
                                );
                            }

                            if (Mage::app()->getGroup($store->getGroupId())->getDefaultStoreId() == $store->getId()) {
                                $this->_baseUrlToStore[$baseUrl][''] = $store->getCode();
                            }

                            $this->_baseUrlToStore[$baseUrl][$specialCode] = $store->getCode();
                        }
                    }
                }
            }
        }
        
        return $this->_baseUrlToStore;
    }

    /**
     * Is https secure request
     *
     * @return boolean
     */
    public function isSecure()
    {
        if ($this->_isSecureFlag !== null) {
            return $this->_isSecureFlag;
        }

        return parent::isSecure();
    }

    /**
     * Returns a secure flag
     *
     * @param bool $isSecure
     * @return $this
     */
    public function setIsSecureFlag($isSecure)
    {
        $this->_isSecureFlag = $isSecure;
        return $this;
    }
}
