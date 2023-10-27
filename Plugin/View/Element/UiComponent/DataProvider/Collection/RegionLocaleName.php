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

namespace MageINIC\CityRegionPostcode\Plugin\View\Element\UiComponent\DataProvider\Collection;

use Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory;
use Magento\Framework\Data\CollectionDataSourceInterface as Collection;

/**
 * CityRegionPostcode RegionLocaleName Class
 */
class RegionLocaleName
{
    /**
     * Get report collection
     *
     * @param CollectionFactory $subject
     * @param Collection $collection
     * @param string $requestName
     * @return Collection
     */
    public function afterGetReport(CollectionFactory $subject, Collection $collection, string $requestName)
    {
        if ($requestName !== 'cityregionpostcode_region_listing_data_source') {
            return $collection;
        }

        $collection->getSelect()->joinLeft(
            ['locale_table' => $collection->getResource()->getTable('directory_country_region_name')],
            'locale_table.region_id = main_table.region_id',
            [
                'region_locales' => new \Zend_Db_Expr(
                    "GROUP_CONCAT(
                        DISTINCT CONCAT(
                            '<div class=\"grid-locale-names-col\">',
                            locale_table.locale,
                            ': ',
                            locale_table.name,
                            '</div>'
                        ) SEPARATOR ''
                    )"
                )
            ]
        );

        $collection->getSelect()->group('main_table.region_id');

        return $collection;
    }
}
