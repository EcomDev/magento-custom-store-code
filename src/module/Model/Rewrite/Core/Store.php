<?php

/**
 * Rewritten store model in order to support multi domain matches 
 * 
 * 
 */
class EcomDev_CustomStoreCode_Model_Rewrite_Core_Store
    extends Mage_Core_Model_Store
{
    const XML_PATH_STORE_USE_SPECIAL_CODE = 'web/url/use_code';
    const XML_PATH_STORE_SPECIAL_CODE_VALUE = 'web/url/code';
    const XML_PATH_UNSECURE_ALTERNATIVE_BASE_URL = 'web/unsecure/alternative_base_url';

    /**
     * List of configuration values cache paths that will be cleaned up
     * in memory on attribute load
     * 
     * @var string[]
     */
    protected $_resetCachedNodes = array(
        self::XML_PATH_UNSECURE_BASE_URL,
        self::XML_PATH_UNSECURE_ALTERNATIVE_BASE_URL,
        self::XML_PATH_UNSECURE_BASE_LINK_URL,
        'web/unsecure/base_skin_url',
        'web/unsecure/base_media_url',
        'web/unsecure/base_js_url',
        self::XML_PATH_SECURE_BASE_URL,
        self::XML_PATH_SECURE_BASE_LINK_URL,
        'web/secure/base_skin_url',
        'web/secure/base_media_url',
        'web/secure/base_js_url'
    );

    /**
     * Cookie domain for secure and not secure scopes
     *
     * @var array
     */
    protected $_secureNotSecure = array(
        'web/cookie/cookie_domain' => array(
            false => 'web/url/cookie_domain_unsecure',
            true => 'web/url/cookie_domain_secure'
        )
    );

    /**
     * Updates url with custom store code
     *
     * @param string $url
     *
     * @return string
     */
    protected function _updatePathUseStoreView($url)
    {
        if (Mage::isInstalled() 
            && $this->getConfig(self::XML_PATH_STORE_USE_SPECIAL_CODE) 
            && ($code = $this->getConfig(self::XML_PATH_STORE_SPECIAL_CODE_VALUE))) {
            $url .= $code . '/';
        }
        
        return $url;
    }

    /**
     * Updates configuration with key and values
     * 
     * @param array $data
     * @return $this
     */
    public function updateConfig(array $data)
    {
        foreach ($data as $key => $value) {
            $this->setConfig($key, $value);
        }
        
        $this->_baseUrlCache = array();
        foreach ($this->_resetCachedNodes as $path) {
            if (isset($this->_configCache[$path])) {
                unset($this->_configCache[$path]);
            }
        }
        
        return $this;
    }

    /***
     * Adds a node that should be cleaned up on override of configuration
     * 
     * @param string $path
     * @return $this
     */
    public function addResetCacheNode($path)
    {
        if (!in_array($path, $this->_resetCachedNodes, true)) {
            $this->_resetCachedNodes[] = $path;
        }
        
        return $this;
    }

    /**
     * Removes a node that should be cleaned up on override of configuration
     * 
     * @param string $path
     * @return $this
     */
    public function removeResetCacheNode($path)
    {
        if (in_array($path, $this->_resetCachedNodes, true)) {
            $index = array_search($path, $this->_resetCachedNodes, true);
            array_splice($this->_resetCachedNodes, $index, 1);
        }
        
        return $this;
    }

    /**
     * Retrieve store configuration data
     *
     * @param   string $path
     * @return  string|null
     */
    public function getConfig($path)
    {
        if (isset($this->_secureNotSecure[$path])) {
            $overridePath = $this->_secureNotSecure[$path][$this->isCurrentlySecure()];
            if ($value = $this->getConfig($overridePath)) {
                return $value;
            }
        }
        
        return parent::getConfig($path);
    }
}
