<?php
/**
 * Copyright © Magmodules.eu. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magmodules\GoogleShopping\Helper;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Magmodules\GoogleShopping\Helper\Category as CategoryHelper;
use Magmodules\GoogleShopping\Helper\Feed as FeedHelper;
use Magmodules\GoogleShopping\Helper\General as GeneralHelper;
use Magmodules\GoogleShopping\Helper\Product as ProductHelper;
use Magmodules\GoogleShopping\Service\Product\InventorySource;

/**
 * Class Source
 *
 * @package Magmodules\GoogleShopping\Helper
 */
class Source extends AbstractHelper
{

    const LIMIT_PREVIEW = 100;

    const XPATH_ID = 'magmodules_googleshopping/data/id_attribute';
    const XPATH_NAME = 'magmodules_googleshopping/data/name_attribute';
    const XPATH_DESCRIPTION = 'magmodules_googleshopping/data/description_attribute';
    const XPATH_IMAGE_SOURCE = 'magmodules_googleshopping/data/image';
    const XPATH_IMAGE_MAIN = 'magmodules_googleshopping/data/main_image';
    const XPATH_CONDITION_TYPE = 'magmodules_googleshopping/data/condition_type';
    const XPATH_CONDITION_DEFAULT = 'magmodules_googleshopping/data/condition_default';
    const XPATH_CONDITION_SOURCE = 'magmodules_googleshopping/data/condition_attribute';
    const XPATH_GTIN = 'magmodules_googleshopping/data/gtin_attribute';
    const XPATH_BRAND = 'magmodules_googleshopping/data/brand_attribute';
    const XPATH_MPN = 'magmodules_googleshopping/data/mpn_attribute';
    const XPATH_IDENTIFIER = 'magmodules_googleshopping/data/identifier_exists';
    const XPATH_COLOR = 'magmodules_googleshopping/data/color_attribute';
    const XPATH_MATERIAL = 'magmodules_googleshopping/data/material_attribute';
    const XPATH_PATTERN = 'magmodules_googleshopping/data/pattern_attribute';
    const XPATH_SIZE = 'magmodules_googleshopping/data/size_attribute';
    const XPATH_SIZETYPE = 'magmodules_googleshopping/data/sizetype_attribute';
    const XPATH_SIZESYTEM = 'magmodules_googleshopping/data/sizesystem_attribute';
    const XPATH_GENDER = 'magmodules_googleshopping/data/gender_attribute';
    const XPATH_EXTRA_FIELDS = 'magmodules_googleshopping/advanced/extra_fields';
    const XPATH_URL_UTM = 'magmodules_googleshopping/advanced/url_utm';
    const XPATH_SHIPPING = 'magmodules_googleshopping/advanced/shipping';
    const XPATH_TAX = 'magmodules_googleshopping/advanced/tax';
    const XPATH_INCLUDE_WEIGHT = 'magmodules_googleshopping/advanced/weight';
    const XPATH_WEIGHT_UNIT = 'general/locale/weight_unit';
    const XPATH_CATEGORY = 'magmodules_googleshopping/data/category';
    const XPATH_VISBILITY = 'magmodules_googleshopping/filter/visbility_enabled';
    const XPATH_VISIBILITY_OPTIONS = 'magmodules_googleshopping/filter/visbility';
    const XPATH_CATEGORY_FILTER = 'magmodules_googleshopping/filter/category_enabled';
    const XPATH_CATEGORY_FILTER_TYPE = 'magmodules_googleshopping/filter/category_type';
    const XPATH_CATEGORY_IDS = 'magmodules_googleshopping/filter/category';
    const XPATH_STOCK = 'magmodules_googleshopping/filter/stock';
    const XPATH_ADVANCED = 'magmodules_googleshopping/generate/advanced';
    const XPATH_PAGING = 'magmodules_googleshopping/generate/paging';
    const XPATH_DEBUG_MEMORY = 'magmodules_googleshopping/generate/debug_memory';
    const XPATH_FILTERS = 'magmodules_googleshopping/filter/filters';
    const XPATH_FILTERS_DATA = 'magmodules_googleshopping/filter/filters_data';
    const XPATH_CONFIGURABLE = 'magmodules_googleshopping/types/configurable';
    const XPATH_CONFIGURABLE_LINK = 'magmodules_googleshopping/types/configurable_link';
    const XPATH_CONFIGURABLE_IMAGE = 'magmodules_googleshopping/types/configurable_image';
    const XPATH_CONFIGURABLE_PARENT_ATTS = 'magmodules_googleshopping/types/configurable_parent_atts';
    const XPATH_CONFIGURABLE_NONVISIBLE = 'magmodules_googleshopping/types/configurable_nonvisible';
    const XPATH_BUNDLE = 'magmodules_googleshopping/types/bundle';
    const XPATH_BUNDLE_LINK = 'magmodules_googleshopping/types/bundle_link';
    const XPATH_BUNDLE_IMAGE = 'magmodules_googleshopping/types/bundle_image';
    const XPATH_BUNDLE_PARENT_ATTS = 'magmodules_googleshopping/types/bundle_parent_atts';
    const XPATH_BUNDLE_NONVISIBLE = 'magmodules_googleshopping/types/bundle_nonvisible';
    const XPATH_GROUPED = 'magmodules_googleshopping/types/grouped';
    const XPATH_GROUPED_LINK = 'magmodules_googleshopping/types/grouped_link';
    const XPATH_GROUPED_IMAGE = 'magmodules_googleshopping/types/grouped_image';
    const XPATH_GROUPED_PARENT_PRICE = 'magmodules_googleshopping/types/grouped_parent_price';
    const XPATH_GROUPED_PARENT_ATTS = 'magmodules_googleshopping/types/grouped_parent_atts';
    const XPATH_GROUPED_NONVISIBLE = 'magmodules_googleshopping/types/grouped_nonvisible';
    const XPATH_MANAGE_STOCK = 'cataloginventory/item_options/manage_stock';
    const XPATH_FORCE_SECURE_URLS = 'magmodules_googleshopping/advanced/force_secure';

    /**
     * @var General
     */
    private $generalHelper;
    /**
     * @var Product
     */
    private $productHelper;
    /**
     * @var Category
     */
    private $categoryHelper;
    /**
     * @var Feed
     */
    private $feedHelper;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var InventorySource
     */
    private $inventorySource;

    /**
     * Source constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param General $generalHelper
     * @param Category $categoryHelper
     * @param Product $productHelper
     * @param Feed $feedHelper
     * @param InventorySource $inventorySource
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        GeneralHelper $generalHelper,
        CategoryHelper $categoryHelper,
        ProductHelper $productHelper,
        FeedHelper $feedHelper,
        InventorySource $inventorySource
    ) {
        $this->generalHelper = $generalHelper;
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->feedHelper = $feedHelper;
        $this->storeManager = $storeManager;
        $this->inventorySource = $inventorySource;
        parent::__construct($context);
    }

    /**
     *
     * @param $storeId
     * @param $type
     *
     * @return array
     * @throws LocalizedException
     */
    public function getConfig($storeId, $type)
    {
        $config = [];
        $config['flat'] = false;
        $config['type'] = $type;
        $config['store_id'] = $storeId;
        $config['website_id'] = $this->storeManager->getStore()->getWebsiteId();
        $config['timestamp'] = $this->generalHelper->getLocaleDate($storeId);
        $config['date_time'] = $this->generalHelper->getDateTime();
        $config['filters'] = $this->getProductFilters($type);
        $config['attributes'] = $this->getAttributes($type, $config['filters']);
        $config['price_config'] = $this->getPriceConfig();
        $config['base_url'] = $this->storeManager->getStore()->getBaseUrl();
        $config['force_secure'] = $this->generalHelper->isSetFlag(self::XPATH_FORCE_SECURE_URLS, $storeId);
        $config['feed_locations'] = $this->feedHelper->getFeedLocation($storeId, $type);
        $config['utm_code'] = $this->generalHelper->getStoreValue(self::XPATH_URL_UTM);
        $config['debug_memory'] = $this->generalHelper->getStoreValue(self::XPATH_DEBUG_MEMORY);
        $config['weight_unit'] = $this->getWeightUnit();
        $config['identifier_exists'] = $this->generalHelper->getStoreValue(self::XPATH_IDENTIFIER);
        $config['default_category'] = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY);
        $config['inventory'] = $this->getInventoryData();
        $config['categories'] = $this->categoryHelper->getCollection(
            $storeId,
            'googleshopping_cat',
            $config['default_category'],
            'googleshopping_cat_exlude'
        );

        return $config;
    }

    /**
     * @param $type
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getProductFilters($type)
    {
        $filters = [];
        $filters['type_id'] = ['simple', 'downloadable', 'virtual'];
        $filters['relations'] = [];
        $filters['exclude_parents'] = [];
        $filters['nonvisible'] = [];
        $filters['parent_attributes'] = [];
        $filters['image'] = [];
        $filters['link'] = [];

        $configurabale = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE);
        switch ($configurabale) {
            case "parent":
                array_push($filters['type_id'], 'configurable');
                break;
            case "simple":
                array_push($filters['relations'], 'configurable');
                array_push($filters['exclude_parents'], 'configurable');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['configurable'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'configurable');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_LINK)) {
                    $filters['link']['configurable'] = $link;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'link');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_IMAGE)) {
                    $filters['image']['configurable'] = $image;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'image_link');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['image_link'];
                    }
                }

                break;
            case "both":
                array_push($filters['type_id'], 'configurable');
                array_push($filters['relations'], 'configurable');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['configurable'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'configurable');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_LINK)) {
                    $filters['link']['configurable'] = $link;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'link');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_CONFIGURABLE_IMAGE)) {
                    $filters['image']['configurable'] = $image;
                    if (isset($filters['parent_attributes']['configurable'])) {
                        array_push($filters['parent_attributes']['configurable'], 'image_url');
                    } else {
                        $filters['parent_attributes']['configurable'] = ['image_url'];
                    }
                }

                break;
        }

        $bundle = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE);
        switch ($bundle) {
            case "parent":
                array_push($filters['type_id'], 'bundle');
                break;
            case "simple":
                array_push($filters['relations'], 'bundle');
                array_push($filters['exclude_parents'], 'bundle');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['bundle'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'bundle');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_LINK)) {
                    $filters['link']['bundle'] = $link;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_IMAGE)) {
                    $filters['image']['bundle'] = $image;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'image_link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['image_link'];
                    }
                }

                break;
            case "both":
                array_push($filters['type_id'], 'bundle');
                array_push($filters['relations'], 'bundle');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_PARENT_ATTS)) {
                    $filters['parent_attributes']['bundle'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'bundle');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_LINK)) {
                    $filters['link']['bundle'] = $link;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_BUNDLE_IMAGE)) {
                    $filters['image']['bundle'] = $image;
                    if (isset($filters['parent_attributes']['bundle'])) {
                        array_push($filters['parent_attributes']['bundle'], 'image_link');
                    } else {
                        $filters['parent_attributes']['bundle'] = ['image_link'];
                    }
                }

                break;
        }

        $grouped = $this->generalHelper->getStoreValue(self::XPATH_GROUPED);
        switch ($grouped) {
            case "parent":
                array_push($filters['type_id'], 'grouped');
                break;
            case "simple":
                array_push($filters['relations'], 'grouped');
                array_push($filters['exclude_parents'], 'grouped');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_PARENT_ATTS)) {
                    $filters['parent_attributes']['grouped'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'grouped');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_LINK)) {
                    $filters['link']['grouped'] = $link;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_IMAGE)) {
                    $filters['image']['grouped'] = $image;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'image_link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['image_link'];
                    }
                }

                break;
            case "both":
                array_push($filters['type_id'], 'grouped');
                array_push($filters['relations'], 'grouped');

                if ($attributes = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_PARENT_ATTS)) {
                    $filters['parent_attributes']['grouped'] = explode(',', $attributes);
                }

                if ($nonVisible = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_NONVISIBLE)) {
                    array_push($filters['nonvisible'], 'grouped');
                }

                if ($link = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_LINK)) {
                    $filters['link']['grouped'] = $link;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['link'];
                    }
                }

                if ($image = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_IMAGE)) {
                    $filters['image']['grouped'] = $image;
                    if (isset($filters['parent_attributes']['grouped'])) {
                        array_push($filters['parent_attributes']['grouped'], 'image_link');
                    } else {
                        $filters['parent_attributes']['grouped'] = ['image_link'];
                    }
                }

                break;
        }

        $visibilityFilter = $this->generalHelper->getStoreValue(self::XPATH_VISBILITY);
        if ($visibilityFilter) {
            $visibility = $this->generalHelper->getStoreValue(self::XPATH_VISIBILITY_OPTIONS);
            $filters['visibility'] = explode(',', $visibility);
            $filters['visibility_parents'] = $filters['visibility'];
        } else {
            $filters['visibility'] = [
                Visibility::VISIBILITY_IN_CATALOG,
                Visibility::VISIBILITY_IN_SEARCH,
                Visibility::VISIBILITY_BOTH,
            ];
            $filters['visibility_parents'] = $filters['visibility'];
            if (!empty($filters['relations'])) {
                array_push($filters['visibility'], Visibility::VISIBILITY_NOT_VISIBLE);
            }
        }

        $filters['limit'] = '';
        if ($type == 'preview') {
            $filters['limit'] = self::LIMIT_PREVIEW;
        } else {
            $advanced = (int)$this->generalHelper->getStoreValue(self::XPATH_ADVANCED);
            $paging = preg_replace('/\D/', '', $this->generalHelper->getStoreValue(self::XPATH_PAGING));
            if ($advanced && ($paging > 0)) {
                $filters['limit'] = $paging;
            }
        }

        $filters['stock'] = $this->generalHelper->getStoreValue(self::XPATH_STOCK);

        $categoryFilter = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_FILTER);
        if ($categoryFilter) {
            $categoryIds = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_IDS);
            $filterType = $this->generalHelper->getStoreValue(self::XPATH_CATEGORY_FILTER_TYPE);
            if (!empty($categoryIds) && !empty($filterType)) {
                $filters['category_ids'] = explode(',', $categoryIds);
                $filters['category_type'] = $filterType;
            }
        }

        $filters['advanced'] = [];
        $productFilters = $this->generalHelper->getStoreValue(self::XPATH_FILTERS);
        if ($productFilters) {
            if ($advFilters = $this->generalHelper->getStoreValueArray(self::XPATH_FILTERS_DATA)) {
                foreach ($advFilters as $advFilter) {
                    array_push($filters['advanced'], $advFilter);
                }
            }
        }

        return $filters;
    }

    /**
     * @param       $type
     * @param array $filters
     *
     * @return array
     */
    public function getAttributes($type, $filters = [])
    {
        $attributes = [];
        $attributes['id'] = [
            'label' => 'g:id',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_ID),
            'max' => 50,
            'parent_selection_disabled' => 1,
            'xpath' => self::XPATH_ID
        ];
        $attributes['name'] = [
            'label' => 'g:title',
            'max' => 150,
            'source' => $this->generalHelper->getStoreValue(self::XPATH_NAME),
            'xpath' => self::XPATH_NAME
        ];
        $attributes['description'] = [
            'label' => 'g:description',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_DESCRIPTION),
            'max' => 5000,
            'actions' => ['striptags'],
            'xpath' => self::XPATH_DESCRIPTION
        ];
        $attributes['link'] = [
            'label' => 'g:link',
            'source' => 'product_url',
            'max' => 2000
        ];
        $attributes['image_link'] = [
            'label' => 'g:image_link',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_IMAGE_SOURCE),
            'main' => $this->generalHelper->getStoreValue(self::XPATH_IMAGE_MAIN),
        ];
        $attributes['price'] = [
            'label' => 'g:price',
            'collection' => 'price',
            'parent_selection_disabled' => 1
        ];
        $attributes['brand'] = [
            'label' => 'g:brand',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_BRAND),
            'max' => 70,
            'xpath' => self::XPATH_BRAND
        ];
        $attributes['gtin'] = [
            'label' => 'g:gtin',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_GTIN),
            'max' => 50,
            'xpath' => self::XPATH_GTIN
        ];
        $attributes['mpn'] = [
            'label' => 'g:mpn',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_MPN),
            'max' => 70,
            'xpath' => self::XPATH_MPN
        ];
        $attributes['condition'] = $this->getConditionSource();
        $attributes['color'] = [
            'label' => 'g:color',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_COLOR),
            'max' => 100,
            'xpath' => self::XPATH_COLOR
        ];
        $attributes['gender'] = [
            'label' => 'g:gender',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_GENDER),

        ];
        $attributes['material'] = [
            'label' => 'g:material',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_MATERIAL),
            'max' => 200,
            'xpath' => self::XPATH_MATERIAL
        ];
        $attributes['pattern'] = [
            'label' => 'g:pattern',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_PATTERN),
            'max' => 100,
            'xpath' => self::XPATH_PATTERN
        ];
        $attributes['size'] = [
            'label' => 'g:size',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_SIZE),
            'max' => 100,
            'xpath' => self::XPATH_SIZE
        ];
        $attributes['size_type'] = [
            'label' => 'g:size_type',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_SIZETYPE),
            'xpath' => self::XPATH_SIZETYPE
        ];
        $attributes['size_system'] = [
            'label' => 'g:size_system',
            'source' => $this->generalHelper->getStoreValue(self::XPATH_SIZESYTEM),
            'xpath' => self::XPATH_SIZESYTEM
        ];

        if ($this->generalHelper->getStoreValue(self::XPATH_INCLUDE_WEIGHT)) {
            $attributes['weight'] = [
                'label' => 'g:shipping_weight',
                'source' => 'weight',
                'suffix' => 'weight_unit',
                'actions' => ['number']
            ];
        }

        $attributes['item_group_id'] = [
            'label' => 'g:item_group_id',
            'source' => $attributes['id']['source'],
            'parent_selection_disabled' => 1,
            'parent' => 2
        ];
        $attributes['is_bundle'] = [
            'label' => 'g:is_bundle',
            'source' => 'type_id',
            'condition' => ['bundle:yes'],
            'parent_selection_disabled' => 1,
        ];
        $attributes['availability'] = [
            'label' => 'g:availability',
            'source' => 'is_in_stock',
            'parent_selection_disabled' => 1,
            'condition' => [
                '1:in stock',
                '0:out of stock'
            ]
        ];

        if ($extraFields = $this->getExtraFields()) {
            $attributes = array_merge($attributes, $extraFields);
        }

        if ($type == 'parent') {
            return $attributes;
        } else {
            $attributes = $this->addAttributeActions($attributes);
            return $this->productHelper->addAttributeData($attributes, $filters);
        }
    }

    /**
     * @return array|bool
     */
    public function getConditionSource()
    {
        $conditionType = $this->generalHelper->getStoreValue(self::XPATH_CONDITION_TYPE);
        if ($conditionType == 'static') {
            return [
                'label' => 'g:condition',
                'static' => $this->generalHelper->getStoreValue(self::XPATH_CONDITION_DEFAULT)
            ];
        }
        if ($conditionType == 'attribute') {
            return [
                'label' => 'g:condition',
                'source' => $this->generalHelper->getStoreValue(self::XPATH_CONDITION_SOURCE)
            ];
        }

        return false;
    }

    /**
     * @return array
     */
    public function getExtraFields()
    {
        $extraFields = [];
        if ($attributes = $this->generalHelper->getStoreValueArray(self::XPATH_EXTRA_FIELDS)) {
            $i = 0;
            foreach ($attributes as $attribute) {
                $label = strtolower(str_replace(' ', '_', $attribute['name']));
                $extraFields['extra_' . $i] = [
                    'label' => $label,
                    'source' => $attribute['attribute']
                ];
                $i++;
            }
        }

        return $extraFields;
    }

    /**
     * @param $attributes
     *
     * @return mixed
     */
    public function addAttributeActions($attributes)
    {
        foreach ($attributes as $key => $attribute) {
            if (!isset($attribute['source']) || !isset($attribute['xpath'])) {
                continue;
            }
            if ($attribute['source'] == 'mm-actions-conditional') {
                if ($condition = $this->parseConditionalField($attribute['xpath'])) {
                    $attributes[$key] = array_merge($attributes[$key], $condition);
                }
            }
            if ($attribute['source'] == 'mm-actions-multi') {
                if ($multi = $this->parseMultiField($attribute['xpath'])) {
                    $attributes[$key] = array_merge($attributes[$key], $multi);
                }
            }
        }
        return $attributes;
    }

    /**
     * @param $xpath
     *
     * @return mixed
     */
    public function parseConditionalField($xpath)
    {
        $xpath = str_replace('_attribute', '_conditional', $xpath);
        $condition = $this->generalHelper->getStoreValue($xpath);

        if (!$condition) {
            return false;
        }

        $condSplit = preg_split("/[?:]+/", str_replace(['(', ')'], '', $condition));
        if (count($condSplit) == 3) {
            preg_match_all("/{{([^}]*)}}/", $condition, $foundAtts);
            return [
                'conditional' => [
                    '*:' . trim($condSplit[2]),
                    trim($condSplit[0]) . ':' . trim($condSplit[1]),
                ],
                'multi' => implode(',', array_unique($foundAtts[1]))
            ];
        }

        return false;
    }

    /**
     * @param $xpath
     *
     * @return array|bool
     */
    public function parseMultiField($xpath)
    {
        $xpath = str_replace('_attribute', '_multi', $xpath);
        $multi = $this->generalHelper->getStoreValue($xpath);

        if (!$multi) {
            return false;
        }

        return ['multi' => $multi];
    }

    /**
     * @return array
     */
    public function getPriceConfig()
    {
        $store = $this->storeManager->getStore();

        $priceFields = [];
        $priceFields['price'] = 'g:price';
        $priceFields['sales_price'] = 'g:sale_price';
        $priceFields['sales_date_range'] = 'g:sale_price_effective_date';
        $priceFields['currency'] = $store->getCurrentCurrency()->getCode();
        $priceFields['use_currency'] = true;
        $priceFields['exchange_rate'] = $store->getBaseCurrency()->getRate($priceFields['currency']);
        $priceFields['grouped_price_type'] = $this->generalHelper->getStoreValue(self::XPATH_GROUPED_PARENT_PRICE);

        if ($this->generalHelper->getStoreValue(self::XPATH_TAX)) {
            $priceFields['incl_vat'] = true;
        }

        return $priceFields;
    }

    /**
     * @return string
     */
    public function getWeightUnit()
    {
        $weightUnit = $this->generalHelper->getStoreValue(self::XPATH_WEIGHT_UNIT);
        $weightUnit = str_replace('kgs', 'kg', $weightUnit);
        $weightUnit = str_replace('lbs', 'lb', $weightUnit);

        if ($weightUnit) {
            return ' ' . $weightUnit;
        }
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    public function getInventoryData()
    {
        $invAtt = [];
        $invAtt['attributes'][] = 'is_in_stock';
        $invAtt['attributes'][] = 'manage_stock';
        $invAtt['attributes'][] = 'use_config_manage_stock';

        $websiteCode = $this->storeManager->getWebsite()->getCode();
        $invAtt['stock_id'] = $this->inventorySource->execute($websiteCode);
        $invAtt['config_manage_stock'] = $this->generalHelper->getStoreValue(self::XPATH_MANAGE_STOCK);

        return $invAtt;
    }

    /**
     * @param                                $dataRow
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $config
     * @param \Magento\Catalog\Model\Product $parent
     *
     * @return string
     */
    public function reformatData($dataRow, $product, $config, $parent)
    {
        if ($config['identifier_exists'] && ($identifierExists = $this->getIdentifierExists($dataRow))) {
            $dataRow = array_merge($dataRow, $identifierExists);
        }
        if (!empty($dataRow['g:image_link'])) {
            if ($imageData = $this->getImageData($dataRow)) {
                $dataRow = array_merge($dataRow, $imageData);
            }
        }
        if ($categoryData = $this->getCategoryData($product, $config, $parent)) {
            $dataRow = array_merge($dataRow, $categoryData);
        }
        if ($shippingPrices = $this->getShippingPrices($dataRow, $config)) {
            $dataRow = array_merge($dataRow, $shippingPrices);
        }

        if ($config['force_secure']) {
            foreach (['g:image_link', 'g:link', 'g:additional_image_link'] as $field) {
                if (isset($dataRow[$field]) && !is_array($dataRow[$field])) {
                    $dataRow[$field] = str_replace('http://', 'https://', $dataRow[$field]);
                }
                if (isset($dataRow[$field]) && is_array($dataRow[$field])) {
                    foreach ($dataRow[$field] as $key => $value) {
                        $dataRow[$field][$key] = str_replace('http://', 'https://', $dataRow[$field][$key]);
                    }
                }
            }
        }

        $xml = $this->getXmlFromArray($dataRow, 'item');

        return $xml;
    }

    /**
     * @param $dataRow
     *
     * @return array|bool
     */
    public function getIdentifierExists($dataRow)
    {
        $identifier = false;
        $identifierExists = [];

        if (!empty($dataRow['g:gtin'])) {
            if (!empty($dataRow['g:brand'])) {
                $identifier = true;
            }
        }

        if (!empty($dataRow['g:mpn'])) {
            if (!empty($dataRow['g:brand'])) {
                $identifier = true;
            }
        }

        if (!$identifier) {
            $identifierExists['g:identifier_exists'] = 'no';
        }

        return $identifierExists;
    }

    /**
     * @param $dataRow
     *
     * @return array
     */
    public function getImageData($dataRow)
    {
        $imageData = [];
        if (is_array($dataRow['g:image_link'])) {
            $imageLinks = $dataRow['g:image_link'];
            foreach ($imageLinks as $link) {
                if (empty($imageData['g:image_link'])) {
                    $imageData['g:image_link'] = $link;
                } else {
                    $imageData['g:additional_image_link'][] = $link;
                }
            }
        } else {
            $imageData['g:image_link'] = $dataRow['g:image_link'];
        }

        if (!empty($imageData['g:additional_image_link']) && count($imageData['g:additional_image_link']) > 10) {
            $imageData['g:additional_image_link'] = array_slice($imageData['g:additional_image_link'], 0, 10);
        }

        return $imageData;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param array $config
     * @param \Magento\Catalog\Model\Product $parent
     *
     * @return array
     */
    public function getCategoryData($product, $config, $parent)
    {
        $path = [];
        $categories = $config['categories'];
        $parentType = $parent !== null ? $parent->getTypeId() : null;

        if ($parentType !== null && !empty($config['filters']['parent_attributes'][$parentType])) {
            if (in_array('product_type', $config['filters']['parent_attributes'][$parentType])) {
                $product = $parent;
            }
        }

        $level = 0;
        foreach ($product->getCategoryIds() as $catId) {
            if (!empty($categories[$catId])) {
                $category = $categories[$catId];
                if ($category['level'] > $level) {
                    $deepestCategory = $category;
                    $level = $category['level'];
                }
            }
        }
        if (!empty($deepestCategory)) {
            $path['g:product_type'] = implode(' > ', $deepestCategory['path']);
            $path['g:google_product_category'] = $deepestCategory['custom'];
        }

        if (!empty($product->getData('googleshopping_category'))) {
            $path['g:google_product_category'] = $product->getData('googleshopping_category');
        }

        return $path;
    }

    /**
     * @param $dataRow
     * @param $config
     *
     * @return array
     */
    public function getShippingPrices($dataRow, $config)
    {
        $shippingPrices = [];
        if (!isset($dataRow['g:price'])) {
            return $shippingPrices;
        }

        if ($shippingArray = $this->generalHelper->getStoreValueArray(self::XPATH_SHIPPING)) {
            $currency = $config['price_config']['currency'];
            $price = (!empty($dataRow['g:sales_price']) ? $dataRow['g:sales_price'] : $dataRow['g:price']);
            $price = preg_replace('/([^0-9\.,])/i', '', $price);
            foreach ($shippingArray as $shipping) {
                if (($price >= $shipping['price_from']) && ($price <= $shipping['price_to'])) {
                    if (isset($shipping['code']) && isset($shipping['service'])) {
                        $shippingPrices['g:shipping'][] = [
                            'g:country' => $shipping['code'],
                            'g:service' => $shipping['service'],
                            'g:price' => number_format($shipping['price'], 2, '.', '') . ' ' . $currency
                        ];
                    }
                }
            }
        }

        return $shippingPrices;
    }

    /**
     * @param $data
     * @param $type
     *
     * @return string
     */
    public function getXmlFromArray($data, $type)
    {
        $xml = '  <' . $type . '>' . PHP_EOL;
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $kk => $vv) {
                    if (!empty($vv) && !is_array($vv)) {
                        $xml .= sprintf('   <%s>%s</%s>', $k, htmlspecialchars($vv, ENT_XML1), $k) . PHP_EOL;
                    } elseif (!empty($vv) && is_array($vv)) {
                        $xml .= '   <' . $k . '>' . PHP_EOL;
                        foreach ($vv as $kkk => $vvv) {
                            $xml .= sprintf('    <%s>%s</%s>', $kkk, htmlspecialchars($vvv, ENT_XML1), $kkk) . PHP_EOL;
                        }
                        $xml .= '   </' . $k . '>' . PHP_EOL;
                    }
                }
            } else {
                if (strlen($v) && is_string($v)) {
                    $xml .= sprintf('   <%s>%s</%s>', $k, htmlspecialchars($v, ENT_XML1), $k) . PHP_EOL;
                }
            }
        }
        $xml .= '  </' . $type . '>' . PHP_EOL;

        return $xml;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param                                $type
     *
     * @return string
     */
    public function getProductDataXml($product, $type)
    {
        $productData = [];
        foreach ($product->getData() as $k => $v) {
            if (!is_array($v)) {
                $productData[$k] = $v;
            }
        }

        return $this->getXmlFromArray($productData, $type);
    }
}
