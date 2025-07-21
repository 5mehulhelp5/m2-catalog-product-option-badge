<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Helper;

use FeWeDev\Base\Json;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Option\Value;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Data
{
    /** @var Json */
    protected $json;

    public function __construct(Json $json)
    {
        $this->json = $json;
    }

    /**
     * @param Option[] $options
     */
    public function getOptionsConfig(array $options): string
    {
        $config = $this->getOptionsConfigData($options);

        return $this->json->encode($config);
    }

    /**
     * @param Option[] $options
     */
    public function getOptionsConfigData(array $options): array
    {
        $config = [];

        foreach ($options as $option) {
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

        return $config;
    }
}
