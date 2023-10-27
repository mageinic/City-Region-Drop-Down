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

namespace MageINIC\CityRegionPostcode\Ui\Model;

use Magento\Directory\Model\ResourceModel\Country\CollectionFactory as CountryCollectionFactory;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * CityRegionPostcode ExtendedCountryCollection Class
 */
class ExtendedCountryCollection implements OptionSourceInterface
{
    /**
     * @var CountryCollectionFactory
     */
    protected CountryCollectionFactory $countryCollectionFactory;
    /**
     * @var RegionCollectionFactory
     */
    protected RegionCollectionFactory $regionCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param CountryCollectionFactory $countryCollectionFactory
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        CountryCollectionFactory $countryCollectionFactory,
        RegionCollectionFactory $regionCollectionFactory
    ) {
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->regionCollectionFactory  = $regionCollectionFactory;
    }

    /**
     * Get List of Available Country From Region
     *
     * @return array
     */
    protected function getAvailableCountryFromRegion()
    {
        $regionCollection = $this->regionCollectionFactory->create();
        $regionCollection->addFieldToSelect('country_id');
        $regionCollection->getSelect()->group('country_id');
        return $regionCollection->getColumnValues('country_id');
    }

    /**
     * Convert collection items to select options array
     *
     * Only collect countries with regions
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $countryIds = $this->getAvailableCountryFromRegion();
        $collection = $this->countryCollectionFactory->create();
        $collection->addCountryIdFilter($countryIds)->load();

        $options = [];
        foreach ($collection as $item) {
            $option = [];
            $option['value'] = $item->getCountryId();
            $option['label'] = $item->getName();
            $options[] = $option;
        }

        $this->sortByKey($options, 'label');

        $this->options = $options;
        if (!empty($options)) {
            array_unshift(
                $options,
                ['title' => '', 'value' => '', 'label' => __('Please select a country.')]
            );
        }
        return $options;
    }

    /**
     * Sort By Key
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
