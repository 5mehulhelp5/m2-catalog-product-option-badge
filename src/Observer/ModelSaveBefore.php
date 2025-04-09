<?php /** @noinspection PhpDeprecationInspection */

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Observer;

use FeWeDev\Base\Arrays;
use FeWeDev\Base\Json;
use FeWeDev\Base\Variables;
use Infrangible\Core\Helper\Stores;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class ModelSaveBefore implements ObserverInterface
{
    /** @var Arrays */
    protected $arrays;

    /** @var Variables */
    protected $variables;

    /** @var Json */
    protected $json;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(Arrays $arrays, Variables $variables, Json $json, Stores $storeHelper)
    {
        $this->arrays = $arrays;
        $this->variables = $variables;
        $this->json = $json;
        $this->storeHelper = $storeHelper;
    }

    /**
     * @throws \Exception
     */
    public function execute(Observer $observer): void
    {
        $object = $observer->getData('object');

        if ($object instanceof Option) {
            $badge = $object->getData('badge');

            if ($this->variables->isEmpty($badge)) {
                $object->setData('badge');
            }

            if (is_array($badge)) {
                if (! $this->arrays->isAssociative($badge)) {
                    $badge = reset($badge);
                }

                $urlFilter = filter_var(
                    $badge[ 'url' ],
                    FILTER_VALIDATE_URL
                );

                if ($urlFilter === false) {
                    if (array_key_exists(
                        'full_path',
                        $badge
                    )) {
                        $fullPath = $badge[ 'full_path' ];
                    } else {
                        $fullPath = substr(
                            $badge[ 'url' ],
                            strpos(
                                $badge[ 'url' ],
                                'wysiwyg/'
                            )
                        );

                        $badge[ 'full_path' ] = $fullPath;
                    }

                    $badge[ 'url' ] = $this->storeHelper->getMediaUrl() . $fullPath;
                }

                $object->setData(
                    'badge',
                    $this->json->encode($badge)
                );
            }
        }

        if ($object instanceof Value) {
            $badge = $object->getData('badge');

            if ($this->variables->isEmpty($badge)) {
                $object->setData('badge');
            }

            if (is_array($badge)) {
                if (! $this->arrays->isAssociative($badge)) {
                    $badge = reset($badge);
                }

                $urlFilter = filter_var(
                    $badge[ 'url' ],
                    FILTER_VALIDATE_URL
                );

                if ($urlFilter === false) {
                    if (array_key_exists(
                        'full_path',
                        $badge
                    )) {
                        $fullPath = $badge[ 'full_path' ];
                    } else {
                        $fullPath = substr(
                            $badge[ 'url' ],
                            strpos(
                                $badge[ 'url' ],
                                'wysiwyg/'
                            )
                        );

                        $badge[ 'full_path' ] = $fullPath;
                    }

                    $badge[ 'url' ] = $this->storeHelper->getMediaUrl() . $fullPath;
                }

                $object->setData(
                    'badge',
                    $this->json->encode($badge)
                );
            }
        }
    }
}
