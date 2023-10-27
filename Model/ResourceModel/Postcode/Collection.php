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

namespace MageINIC\CityRegionPostcode\Model\ResourceModel\Postcode;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * CityRegionPostcode ResourceModel Collection Class
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'postcode_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \MageINIC\CityRegionPostcode\Model\Postcode::class,
            \MageINIC\CityRegionPostcode\Model\ResourceModel\Postcode::class
        );
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $propertyMap = [
            'value'         => 'postcode_id',
            'title'         => 'postcode',
            'country_id'    => 'country_id',
            'region_id'     => 'region_id',
            'city_id'       => 'city_id'
        ];

        foreach ($this as $item) {
            $option =  [];
            foreach ($propertyMap as $code => $field) {
                $option[$code] = $item->getData($field);
            }
            $option['label'] = $item->getPostcode();
            $options[] = $option;
        }

        array_unshift(
            $options,
            ['title' => '', 'value' => '', 'label' => __('Please Select a Zip/Postal Code.')]
        );
        return $options;
    }
}
