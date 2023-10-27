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

namespace MageINIC\CityRegionPostcode\Model\ResourceModel\Address\Attribute\Source;

use MageINIC\CityRegionPostcode\Model\ResourceModel\Postcode\Collection;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory as AttributeOptionCollectionFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory as AttributeOptionFactory;
use MageINIC\CityRegionPostcode\Model\ResourceModel\Postcode\CollectionFactory as PostcodeCollectionFactory;

/**
 * CityRegionPostcode Address Attribute Source Postcode Class
 */
class Postcode extends Table
{
    /**
     * @var PostcodeCollectionFactory
     */
    private PostcodeCollectionFactory $postcodeCollectionFactory;

    /**
     * @param AttributeOptionCollectionFactory $attrOptionCollectionFactory
     * @param AttributeOptionFactory $attrOptionFactory
     * @param PostcodeCollectionFactory $postcodeCollectionFactory
     */
    public function __construct(
        AttributeOptionCollectionFactory $attrOptionCollectionFactory,
        AttributeOptionFactory           $attrOptionFactory,
        PostcodeCollectionFactory            $postcodeCollectionFactory
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->postcodeCollectionFactory = $postcodeCollectionFactory;
    }

    /**
     * Retrieve Full Option values array
     *
     * @param bool $withEmpty       Add empty option to array
     * @param bool $defaultValues
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false): array
    {
        if (!$this->_options) {
            $this->_options = $this->createPostcodeCollection()->load()->toOptionArray();
        }
        return $this->_options;
    }

    /**
     * Get List of Postcode Collection
     *
     * @return Collection
     */
    protected function createPostcodeCollection(): Collection
    {
        return $this->postcodeCollectionFactory->create();
    }
}
