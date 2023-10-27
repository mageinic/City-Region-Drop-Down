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

namespace MageINIC\CityRegionPostcode\Model\Config\Source;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory as RegionCollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * CityRegionPostcode Class Region
 */
class Region implements OptionSourceInterface
{
    /**
     * @var array|null
     */
    protected ?array $_options = null;

    /**
     * @var RegionCollectionFactory
     */
    private RegionCollectionFactory $regionCollectionFactory;

    /**
     * @param RegionCollectionFactory $regionCollectionFactory
     */
    public function __construct(
        RegionCollectionFactory $regionCollectionFactory
    ) {
        $this->regionCollectionFactory = $regionCollectionFactory;
    }

    /**
     * Get OptionText
     *
     * @param  mixed $value
     * @return false|mixed
     */
    public function getOptionText(mixed $value): mixed
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    /**
     * Get AllOptions
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions(bool $withEmpty = false)
    {
        if ($this->_options === null) {
            $_options = [];
            $regionCollection = $this->regionCollectionFactory->create();
            $regionCollection->getSelect()->order('country_id ASC');
            foreach ($regionCollection as $region) {
                $_options[] = [
                    'value' => $region->getId(),
                    'label' => $region->getName()
                ];
            }
            $this->_options = $_options;
        }

        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, ['value' => '', 'label' => '']);
        }
        return $options;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    /**
     * To OptionHash
     *
     * @param bool $withEmpty
     * @return array
     */
    public function toOptionHash(bool $withEmpty = true)
    {
        return $this->getOptionsArray($withEmpty);
    }

    /**
     * Get OptionsArray
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getOptionsArray(bool $withEmpty = true)
    {
        $options = [];
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }
}
