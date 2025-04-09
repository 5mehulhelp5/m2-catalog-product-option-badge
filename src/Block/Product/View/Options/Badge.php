<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Block\Product\View\Options;

use FeWeDev\Base\Json;
use Infrangible\Core\Helper\Registry;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\View\Element\Template;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Badge extends Template
{
    /** @var Registry */
    protected $registryHelper;

    /** @var Json */
    protected $json;

    /** @var Product */
    private $product;

    public function __construct(Template\Context $context, Registry $registryHelper, Json $json, array $data = [])
    {
        parent::__construct(
            $context,
            $data
        );

        $this->registryHelper = $registryHelper;
        $this->json = $json;
    }

    public function getProduct(): Product
    {
        if (! $this->product) {
            if ($this->registryHelper->registry('current_product')) {
                $this->product = $this->registryHelper->registry('current_product');
            } else {
                throw new \LogicException('Product is not defined');
            }
        }

        return $this->product;
    }

    public function getOptionsConfig(): string
    {
        $config = [];

        foreach ($this->getProduct()->getOptions() as $option) {
            $badge = $option->getData('badge');

            if (is_array($badge)) {
                $badge = reset($badge);

                $config[ $option->getId() ] = $badge[ 'url' ];
            }

            $values = $option->getValues();

            if (is_array($values)) {
                /** @var Value $value */
                foreach ($values as $value) {
                    $badge = $value->getData('badge');

                    if (is_array($badge)) {
                        $badge = reset($badge);

                        $config[ $option->getId() ][ 'values' ][ $value->getId() ] = $badge[ 'url' ];
                    }
                }
            }
        }

        return $this->json->encode($config);
    }
}
