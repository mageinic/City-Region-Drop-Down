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

namespace MageINIC\CityRegionPostcode\Model\Source;

use MageINIC\CityRegionPostcode\Model\ResourceModel\City as CityResource;
use Magento\Directory\Model\ResourceModel\Region\CollectionFactory  as RegionCollection;
use Magento\Framework\Data\OptionSourceInterface;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City\CollectionFactory;

/**
 * CityRegionPostcode Source City Class
 */
class City implements OptionSourceInterface
{
    /**
     * @var RegionCollection
     */
    protected RegionCollection $collectionFactory;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $cityCollectionFactory;

    /**
     * @param RegionCollection $collectionFactory
     * @param CollectionFactory $cityCollectionFactory
     */
    public function __construct(
        RegionCollection  $collectionFactory,
        CollectionFactory $cityCollectionFactory
    ) {
        $this->collectionFactory     = $collectionFactory;
        $this->cityCollectionFactory = $cityCollectionFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        $options[] = ['label' => '-- Please Select --', 'value' => ''];
        $collection = $this->cityCollectionFactory->create();

        foreach ($collection as $category) {
            $options[] = [
                'label' => $category->getDefaultName(),
                'value' => $category->getCityId(),
            ];
        }

        return $options;
    }
}
