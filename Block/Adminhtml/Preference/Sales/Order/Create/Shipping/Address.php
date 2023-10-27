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

namespace MageINIC\CityRegionPostcode\Block\Adminhtml\Preference\Sales\Order\Create\Shipping;

use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Framework\Data\Form\AbstractForm;
use Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Address as CoreAddress;

/**
 * cityRegionPostcode Create Shipping Address Class
 */
class Address extends CoreAddress
{
    /**
     * Add Attributes To Form
     *
     * @param AttributeMetadataInterface[] $attributes
     * @param AbstractForm $form
     * @return $this|Address
     */
    protected function _addAttributesToForm($attributes, AbstractForm $form)
    {
        // Custom sorting
        $addressOrder = [
            'country_id' => 0,
            'region' => 1,
            'region_id' => 2,
            'city' => 3,
            'city_id' => 4,
            'postcode' => 5,
            'postcode_id' => 6
        ];
        $attributes = $this->customSortAttributes($attributes, $addressOrder);

        // add additional form types
        $types = $this->_getAdditionalFormElementTypes();
        foreach ($types as $type => $className) {
            $form->addType($type, $className);
        }
        $renderers = $this->_getAdditionalFormElementRenderers();
        foreach ($attributes as $attribute) {
            $inputType = $attribute->getFrontendInput();

            if ($inputType) {
                $element = $form->addField(
                    $attribute->getAttributeCode(),
                    $inputType,
                    [
                        'name' => $attribute->getAttributeCode(),
                        'label' => __($attribute->getStoreLabel()),
                        'class' => $this->getValidationClasses($attribute),
                        'required' => $attribute->isRequired(),
                    ]
                );
                if ($inputType == 'multiline') {
                    $element->setLineCount($attribute->getMultilineCount());
                }
                $element->setEntityAttribute($attribute);
                $this->_addAdditionalFormElementData($element);

                if (!empty($renderers[$attribute->getAttributeCode()])) {
                    $element->setRenderer($renderers[$attribute->getAttributeCode()]);
                }

                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $options = [];
                    foreach ($attribute->getOptions() as $optionData) {
                        $data = $this->dataObjectProcessor->buildOutputDataArray(
                            $optionData,
                            \Magento\Customer\Api\Data\OptionInterface::class
                        );
                        foreach ($data as $key => $value) {
                            if (is_array($value)) {
                                unset($data[$key]);
                                $data['value'] = $value;
                            }
                        }
                        $options[] = $data;
                    }
                    $element->setValues($options);
                } elseif ($inputType == 'date') {
                    $format = $this->_localeDate->getDateFormat(
                        \IntlDateFormatter::SHORT
                    );
                    $element->setDateFormat($format);
                }
            }
        }

        return $this;
    }

    /**
     * Custom Sort Attributes
     *
     * @param AttributeMetadataInterface[] $attributes
     * @param array $order
     * @return array
     */
    public function customSortAttributes(array $attributes, array $order)
    {
        $insertAt = 0;
        foreach ($attributes as $key => $_) {
            if (isset($order[$key])) {
                break;
            }
            $insertAt++;
        }

        $special = [];
        foreach ($order as $key => $_) {
            if (isset($attributes[$key])) {
                $special[$key] = $attributes[$key];
            }
        }

        $result = [];
        foreach ($attributes as $key => $value) {
            if (!isset($order[$key])) {
                $result[$key] = $value;
            } elseif (count($result) == $insertAt) {
                $result = array_merge($result, $special);
            }
        }
        return $result;
    }
}
