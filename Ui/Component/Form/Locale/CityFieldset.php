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

namespace MageINIC\CityRegionPostcode\Ui\Component\Form\Locale;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;
use MageINIC\CityRegionPostcode\Model\Config\Source\Locale;

/**
 * CityRegionPostcode Form Locale CityFieldset Class
 */
class CityFieldset extends BaseFieldset
{
    /**
     * @var FieldFactory
     */
    protected FieldFactory $fieldFactory;

    /**
     * @var Locale
     */
    protected Locale $locale;

    /**
     * @param ContextInterface $context
     * @param FieldFactory $fieldFactory
     * @param Locale $locale
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        FieldFactory $fieldFactory,
        Locale $locale,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
        $this->locale = $locale;
    }

    /**
     * Get ChildComponents
     *
     * @return UiComponentInterface[]
     * @throws LocalizedException
     */
    public function getChildComponents()
    {
        $locales = $this->locale->toOptionArray();
        foreach ($locales as $locale) {
            $fieldInstance = $this->fieldFactory->create();
            $fieldName = 'city_locales[' . $locale["value"] . ']';
            $fieldLabel = $locale["label"];
            $dataScope = 'city_locales.' . $locale["value"];

            $fieldInstance->setData(
                [
                    'config' =>   [
                        'label' => $fieldLabel,
                        'formElement' => 'input',
                        'disabled'=>false,
                        'source' => $dataScope,
                        'dataScope' => $dataScope,
                        'tooltip' => [
                            'description' => __('Only fill up if you want to override the Default value')
                        ]
                    ],
                    'name' => $fieldName
                ]
            );
            $fieldInstance->prepare();
            $this->addComponent($fieldName, $fieldInstance);
        }

        return parent::getChildComponents();
    }
}
