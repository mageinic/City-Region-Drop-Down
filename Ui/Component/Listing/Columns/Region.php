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

namespace MageINIC\CityRegionPostcode\Ui\Component\Listing\Columns;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use MageINIC\CityRegionPostcode\Helper\Data as CityRegionPostcodeHelper;
use MageINIC\CityRegionPostcode\Model\Config\Source\Region as RegionSource;

/**
 * CityRegionPostcode Region Columns Class
 */
class Region extends Column implements OptionSourceInterface
{
    /**
     * @var CityRegionPostcodeHelper
     */
    protected CityRegionPostcodeHelper $cityRegionPostcodeHelper;
    /**
     * @var RegionSource
     */
    private RegionSource $region;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CityRegionPostcodeHelper $cityRegionPostcodeHelper
     * @param RegionSource $region
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CityRegionPostcodeHelper $cityRegionPostcodeHelper,
        RegionSource $region,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->cityRegionPostcodeHelper = $cityRegionPostcodeHelper;
        $this->region = $region;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = $this->region->toOptionArray();
        $this->sortByKey($options, 'label');
        return $options;
    }

    /**
     * Sort ByKey
     *
     * @param array $data
     * @param string $key
     * @return void
     */
    private function sortByKey(array &$data, string $key)
    {
        usort($data, function ($a, $b) use ($key) {
            return strcmp($a[$key], $b[$key]);
        });
    }
}
