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

namespace MageINIC\CityRegionPostcode\Block\Adminhtml\Sales\Order\Address\Form\Renderer;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use MageINIC\CityRegionPostcode\ViewModel\CityViewModel;

/**
 * CityRegionPostcode Address Form Renderer CityId Class
 */
class CityId extends AbstractBlock implements RendererInterface
{
    /**
     * @var CityViewModel
     */
    private CityViewModel $cityViewModel;

    /**
     * @param Context $context
     * @param CityViewModel $cityViewModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        CityViewModel $cityViewModel,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cityViewModel = $cityViewModel;
    }

    /**
     * Render form element as HTML
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if (! ($country = $element->getForm()->getElement('country_id'))
            || ! ($region = $element->getForm()->getElement('region_id'))
        ) {
            $regionId = $region->getValue();
            return $regionId;
        } else {
            $element->getDefaultHtml();
        }

        $cityId = $element->getForm()->getElement('city_id')->getValue();
        $html = '<div class="field field-state required admin__field _required">';
        $element->setClass('input-text admin__control-text');
        $element->setRequired(true);
        $html .= $element->getLabelHtml() . '<div class="control admin__field-control">';
        $html .= $element->getElementHtml();

        $selectName = str_replace('city', 'city_id', $element->getName());
        $selectId = $element->getHtmlId() . '_id';

        $html .= '<select id="' .
            $selectId .
            '" name="' .
            $selectName .
            '" class="select required-entry admin__control-select" style="display:none">';
        $html .= '<option value="">' . __('Please select') . '</option>';
        $html .= '</select>';

        $html .= '<script>' . "\n";
        $html .= 'require([';
        $html .= '"prototype", "mage/adminhtml/form", "MageINIC_CityRegionPostcode/js/city-updater"';
        $html .= '], function() {';
        $html .= '$("' . $selectId . '").setAttribute("defaultValue", "' . $cityId . '");' . "\n";
        $html .= 'new CityUpdater("' .
            $country->getHtmlId() .
            '", "' .
            $region->getHtmlId() .
            '", "' .
            $element->getForm()->getElement('region')->getHtmlId() .
            '", "' .
            $element->getHtmlId() .
            '", "' .
            $selectId .
            '", ' .
            $this->cityViewModel->getCitySerializeValues() .
            ');' .
            "\n";
        $html .= '});';
        $html .= '</script>' . "\n";
        $html .= '</div></div>' . "\n";
        return $html;
    }
}
