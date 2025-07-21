<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Block\Product\View\Options;

use Infrangible\CatalogProductOptionBadge\Helper\Data;
use Infrangible\Core\Helper\Registry;
use LogicException;
use Magento\Catalog\Model\Product;
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

    /** @var Data */
    protected $helper;

    /** @var Product */
    private $product;

    public function __construct(Template\Context $context, Registry $registryHelper, Data $helper, array $data = [])
    {
        parent::__construct(
            $context,
            $data
        );

        $this->registryHelper = $registryHelper;
        $this->helper = $helper;
    }

    public function getProduct(): Product
    {
        if (! $this->product) {
            if ($this->registryHelper->registry('current_product')) {
                $this->product = $this->registryHelper->registry('current_product');
            } else {
                throw new LogicException('Product is not defined');
            }
        }

        return $this->product;
    }

    public function getOptionsConfig(): string
    {
        return $this->helper->getOptionsConfig($this->getProduct()->getOptions());
    }
}
