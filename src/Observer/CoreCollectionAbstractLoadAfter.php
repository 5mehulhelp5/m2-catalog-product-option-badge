<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Observer;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\ResourceModel\Product\Option\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class CoreCollectionAbstractLoadAfter implements ObserverInterface
{
    /** @var Variables */
    protected $variables;

    /** @var Json */
    protected $json;

    public function __construct(Variables $variables, Json $json)
    {
        $this->variables = $variables;
        $this->json = $json;
    }

    public function execute(Observer $observer): void
    {
        $collection = $observer->getData('collection');

        if ($collection instanceof Collection) {
            /** @var Option $option */
            foreach ($collection as $option) {
                $badge = $option->getData('badge');

                if (! $this->variables->isEmpty($badge) && ! is_array($badge)) {
                    $option->setData(
                        'badge',
                        [$this->json->decode($badge)]
                    );
                }

                $values = $option->getValues();

                if ($values) {
                    /** @var Option\Value $value */
                    foreach ($values as $value) {
                        $badge = $value->getData('badge');

                        if (! $this->variables->isEmpty($badge)) {
                            $value->setData(
                                'badge',
                                [$this->json->decode($badge)]
                            );
                        }
                    }
                }
            }
        }

        if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Product\Option\Value\Collection) {
            /** @var Option\Value $value */
            foreach ($collection as $value) {
                $badge = $value->getData('badge');

                if (! $this->variables->isEmpty($badge)) {
                    $value->setData(
                        'badge',
                        [$this->json->decode($badge)]
                    );
                }
            }
        }
    }
}
