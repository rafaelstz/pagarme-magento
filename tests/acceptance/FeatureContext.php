<?php

use Behat\Behat\Context\Context;
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;

class FeatureContext implements Context
{
    public static $websiteId;

    public static $store;

    public static $customer;

    public static $product;

    /**
     * @BeforeSuite
     */
    public static function setup(BeforeSuiteScope $scope)
    {
        Mage::init();

        self::$websiteId = Mage::app()
            ->getWebsite()
            ->getId();

        self::$store = Mage::app()
            ->getStore();

        Mage::app()
            ->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

        Mage::getModel('cms/page')
            ->load(2)
            ->setData('content', '{{block type="catalog/product_list" name="home.catalog.product.list" alias="products_homepage" category_id="9" template="catalog/product/list.phtml"}}')
            ->save();

        self::createACustomer();
        self::createAProduct();

        Mage::app()
            ->getCacheInstance()
            ->flush();
    }

    public function createACustomer()
    {
        $websiteId = Mage::app()
            ->getWebsite()
            ->getId();

        $store = Mage::app()
            ->getStore();

        $customer = Mage::getModel('customer/customer')
            ->setWebsiteId(self::$websiteId)
            ->setStore(self::$store)
            ->setFirstname('Lívia Nina')
            ->setLastname('Isabelle Freitas')
            ->setTaxvat('41.724.895-7')
            ->setDob('03/12/1980')
            ->setEmail(mktime() . 'livia_nina@arganet.com.br')
            ->setPassword('q6Cyxg4TMM');

        $customer->save();

        $address = Mage::getModel('customer/address')
            ->setData(
                array(
                    'firstname'  => 'Lívia Nina',
                    'lastname'   => 'Isabelle Freitas',
                    'street'     => array(
                        '0' => 'Rua Siqueira Campos',
                        '1' => '515',
                        '2' => '',
                        '3' => 'Jacintinho'
                    ),
                    'city'       => 'Maceió',
                    'region_id'  => '',
                    'region'     => 'SP',
                    'postcode'   => '57040460',
                    'country_id' => 'BR',
                    'telephone'  => '(82) 99672-3631'
                )
            )
            ->setCustomerId($customer->getId())
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');

        $address->save();

        self::$customer = $customer;

    }

    public static function getCustomer()
    {
        return self::$customer;
    }

    public static function createAProduct()
    {
        $attributeSetId = Mage::getSingleton('eav/config')
            ->getEntityType(Mage_Catalog_Model_Product::ENTITY)
            ->getDefaultAttributeSetId();

        $product = Mage::getModel('catalog/product')
            ->setWebsiteIds(array(1))
            ->setAttributeSetId($attributeSetId)
            ->setTypeId('simple')
            ->setCreatedAt(strtotime('now'))
            ->setSku('testsku61')
            ->setName('test product21')
            ->setWeight(4.0000)
            ->setStatus(1)
            ->setTaxClassId(4)
            ->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
            ->setManufacturer(28)
            ->setColor(24)
            ->setNewsFromDate('06/26/2014')
            ->setNewsToDate('06/30/2030')
            ->setCountryOfManufacture('BR')
            ->setPrice(11.22)
            ->setCost(22.33)
            ->setSpecialPrice(20.00)
            ->setSpecialFromDate('06/1/2014')
            ->setSpecialToDate('06/30/2030')
            ->setMsrpEnabled(1)
            ->setMsrpDisplayActualPriceType(1)
            ->setMsrp(99.99)
            ->setMetaTitle('test meta title 2')
            ->setMetaKeyword('test meta keyword 2')
            ->setMetaDescription('test meta description 2')
            ->setDescription('This is a long description')
            ->setShortDescription('This is a short description')
            ->setCategoryIds(array(1));

        $product->save();

        $stock = Mage::getModel('cataloginventory/stock_item');
        $stock->assignProduct($product)
            ->setData('stock_id', 1)
            ->setData('store_id', self::$store->getId())
            ->setData('manage_stock', 1)
            ->setData('is_in_stock', 1)
            ->setData('qty', 999);

        $stock->save();

        self::$product = $product;
    }

    /**
     * @AfterSuite
     */
    public static function teardown(AfterSuiteScope $event)
    {
        //self::$customer->delete();
        //self::$product->delete();
    }
}
