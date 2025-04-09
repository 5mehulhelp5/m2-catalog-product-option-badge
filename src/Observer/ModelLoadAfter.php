<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Observer;

use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ModelLoadAfter implements ObserverInterface
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
        $object = $observer->getData('object');

        if ($object instanceof Option) {
            $badge = $object->getData('badge');

            if (! $this->variables->isEmpty($badge) && ! is_array($badge)) {
                $object->setData(
                    'badge',
                    [$this->json->decode($badge)]
                );
            }

            $values = $object->getValues();

            if ($values) {
                /** @var Value $value */
                foreach ($values as $value) {
                    $badge = $value->getData('badge');

                    if (! $this->variables->isEmpty($badge) && ! is_array($badge)) {
                        $value->setData(
                            'badge',
                            [$this->json->decode($badge)]
                        );
                    }
                }
            }
        }

        if ($object instanceof Value) {
            $badge = $object->getData('badge');

            if (! $this->variables->isEmpty($badge) && ! is_array($badge)) {
                $object->setData(
                    'badge',
                    [$this->json->decode($badge)]
                );
            }
        }
    }
}
