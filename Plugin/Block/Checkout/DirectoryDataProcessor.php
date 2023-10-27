<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_CityRegionPostcode
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\CityRegionPostcode\Plugin\Block\Checkout;

use Magento\Checkout\Block\Checkout\DirectoryDataProcessor as CoreDirectoryDataProcessor;
use MageINIC\CityRegionPostcode\Helper\Data as HelperData;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City\CollectionFactory as CityCollectionFactory;
use MageINIC\CityRegionPostcode\Model\ResourceModel\Postcode\CollectionFactory as PostcodeCollectionFactory;

/**
 * CityRegionPostcode Checkout DirectoryDataProcessor Class
 */
class DirectoryDataProcessor
{
    /**
     * @var CityCollectionFactory
     */
    private CityCollectionFactory $cityCollectionFactory;
    /**
     * @var PostcodeCollectionFactory
     */
    private PostcodeCollectionFactory $postcodeCollectionFactory;
    /**
     * @var HelperData
     */
    private HelperData $helperData;

    /**
     * @param HelperData $helperData
     * @param CityCollectionFactory $cityCollectionFactory
     * @param PostcodeCollectionFactory $postcodeCollectionFactory
     */
    public function __construct(
        HelperData $helperData,
        CityCollectionFactory $cityCollectionFactory,
        PostcodeCollectionFactory $postcodeCollectionFactory
    ) {
        $this->cityCollectionFactory     = $cityCollectionFactory;
        $this->postcodeCollectionFactory = $postcodeCollectionFactory;
        $this->helperData                = $helperData;
    }

    /**
     * After Process
     *
     * @param CoreDirectoryDataProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        CoreDirectoryDataProcessor $subject,
        array $jsLayout
    ) {
        if ($this->helperData->isActive()) {
            if (isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
                if ($this->helperData->isCityActive()) {
                    $jsLayout['components']['checkoutProvider']['dictionaries']['city_id'] = $this->getCityOptions();
                }
            }

            if (isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
                if ($this->helperData->isPostcodeActive()) {
                    $jsLayout['components']['checkoutProvider']['dictionaries']['postcode_id'] =
                        $this->getPostcodeOptions();
                }
            }
        }
        return $jsLayout;
    }

    /**
     * Get city Option's
     *
     * @return array
     */
    private function getCityOptions()
    {
        $options = $this->cityCollectionFactory->create()->toOptionArray();
        $this->sortByKey($options, 'label');
        return $options;
    }

    /**
     * Get postcode Option's
     *
     * @return array
     */
    private function getPostcodeOptions()
    {
        $options = $this->postcodeCollectionFactory->create()->toOptionArray();
        $this->sortByKey($options, 'label');
        return $options;
    }

    /**
     * SortByKey
     *
     * @param array $data
     * @param string $key
     * @return void
     */
    private function sortByKey(&$data, $key)
    {
        usort($data, function ($a, $b) use ($key) {
            return strcmp($a[$key], $b[$key]);
        });
    }
}
