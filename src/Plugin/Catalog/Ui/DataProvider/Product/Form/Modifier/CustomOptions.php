<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;

use FeWeDev\Base\Arrays;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Field;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CustomOptions
{
    public const FIELD_BADGE_NAME = 'badge';

    /** @var Arrays */
    protected $arrays;

    public function __construct(Arrays $arrays)
    {
        $this->arrays = $arrays;
    }

    /**
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject,
        array $meta
    ): array {
        $meta = $this->arrays->addDeepValue(
            $meta,
            [
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GROUP_CUSTOM_OPTIONS_NAME,
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GRID_OPTIONS_NAME,
                'children',
                'record',
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_OPTION,
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_COMMON_NAME,
                'children',
                static::FIELD_BADGE_NAME
            ],
            $this->getBadgeFieldConfig(90)
        );

        return $this->arrays->addDeepValue(
            $meta,
            [
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GROUP_CUSTOM_OPTIONS_NAME,
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GRID_OPTIONS_NAME,
                'children',
                'record',
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::CONTAINER_OPTION,
                'children',
                \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions::GRID_TYPE_SELECT_NAME,
                'children',
                'record',
                'children',
                static::FIELD_BADGE_NAME
            ],
            $this->getBadgeFieldConfig(110)
        );
    }

    protected function getBadgeFieldConfig(int $sortOrder): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'             => __('Badge'),
                        'componentType'     => Field::NAME,
                        'dataScope'         => static::FIELD_BADGE_NAME,
                        'dataType'          => Text::NAME,
                        'sortOrder'         => $sortOrder,
                        'formElement'       => 'imageUploader',
                        'elementTmpl'       => 'ui/form/element/uploader/image',
                        'previewTmpl'       => 'Magento_Catalog/image-preview',
                        'uploaderConfig'    => [
                            'url' => 'product_option_badge/image/upload'
                        ],
                        'additionalClasses' => 'catalog-product-option-badge'
                    ]
                ]
            ]
        ];
    }
}
