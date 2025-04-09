<?php

declare(strict_types=1);

namespace Infrangible\CatalogProductOptionBadge\Controller\Adminhtml\Image;

use FeWeDev\Base\Arrays;
use Infrangible\Core\Controller\Adminhtml\Json;
use Infrangible\Core\Helper\Stores;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

/**
 * @author      Andreas Knollmann
 * @copyright   2014-2025 Softwareentwicklung Andreas Knollmann
 * @license     http://www.opensource.org/licenses/mit-license.php MIT
 */
class Upload extends Json
{
    /** @var ImageUploader */
    protected $imageUploader;

    /** @var Stores */
    protected $storeHelper;

    public function __construct(
        Arrays $arrays,
        \FeWeDev\Base\Json $json,
        Context $context,
        LoggerInterface $logging,
        ImageUploader $imageUploader,
        Stores $storeHelper
    ) {
        parent::__construct(
            $arrays,
            $json,
            $context,
            $logging
        );

        $this->imageUploader = $imageUploader;
        $this->storeHelper = $storeHelper;
    }

    public function execute()
    {
        foreach ($_FILES as $fileId => $fileInfo) {
            foreach (['name', 'full_path', 'type', 'tmp_name', 'error', 'size'] as $key) {
                if (array_key_exists(
                    $key,
                    $fileInfo
                )) {
                    $fileInfoKey = $fileInfo[ $key ];

                    if (array_key_exists(
                        'options',
                        $fileInfoKey
                    )) {
                        foreach ($fileInfoKey[ 'options' ] as $options) {
                            if (array_key_exists(
                                'values',
                                $options
                            )) {
                                foreach ($options[ 'values' ] as $value) {
                                    if (array_key_exists(
                                        'badge',
                                        $value
                                    )) {
                                        $_FILES[ $fileId ][ $key ] = $value[ 'badge' ];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        try {
            $fileIds = array_keys($_FILES);
            $fileId = reset($fileIds);

            $this->imageUploader->setBaseTmpPath('tmp/custom_options');
            $this->imageUploader->setBasePath('wysiwyg/custom_options');
            $this->imageUploader->setAllowedExtensions(['jpg', 'jpeg', 'png', 'gif']);

            $result = $this->imageUploader->saveFileToTmpDir($fileId);
            $imageName = $this->imageUploader->moveFileFromTmp(
                $result[ 'name' ],
                true
            );

            $this->setResponseValues(
                [
                    'name' => basename($imageName),
                    'full_path' => $imageName,
                    'type' => $result[ 'type' ],
                    'tmp_name' => $imageName,
                    'error' => $result[ 'error' ],
                    'size' => $result[ 'size' ],
                    'file' => basename($imageName),
                    'url' => $this->storeHelper->getMediaUrl() . $imageName,
                ]
            );
        } catch (LocalizedException $exception) {
            $this->setResponseValues(['error' => $exception->getMessage(), 'errorcode' => $exception->getCode()]);
        }
    }
}
