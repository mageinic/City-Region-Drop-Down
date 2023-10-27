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

use Magento\Directory\Model\Config\Source\Country as CountryDirectory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use MageINIC\CityRegionPostcode\Helper\Data as CityRegionPostcodeHelper;

/**
 * CityRegionPostcode Country Option Class
 */
class Country extends Column implements OptionSourceInterface
{
    /**
     * @var CityRegionPostcodeHelper
     */
    protected CityRegionPostcodeHelper $cityRegionPostcodeHelper;
    /**
     * @var CountryDirectory
     */
    private CountryDirectory $country;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CityRegionPostcodeHelper $cityRegionPostcodeHelper
     * @param CountryDirectory $country
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CityRegionPostcodeHelper $cityRegionPostcodeHelper,
        CountryDirectory $country,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->cityRegionPostcodeHelper = $cityRegionPostcodeHelper;
        $this->country = $country;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray(): array
    {
        $options = $this->country->toOptionArray(true);
        return $this->formatLabel($options);
    }

    /**
     * Format Label
     *
     * @param array $options
     * @return array
     */
    private function formatLabel(array $options): array
    {
        return array_map(function ($value) {
            $value['label'] = $value['label'] . sprintf(' (%s)', $value['value']);
            return $value;
        }, $options);
    }
}
