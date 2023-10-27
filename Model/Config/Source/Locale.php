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

use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Locale\ListsInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * CityRegionPostcode Locale OptionSourceInterface Class
 */
class Locale implements OptionSourceInterface
{
    /**
     * @var StoreManagerInterface
     */
    private StoreManagerInterface $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var ListsInterface
     */
    private ListsInterface $localeLists;

    /**
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ListsInterface $localeLists
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ListsInterface $localeLists
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig  = $scopeConfig;
        $this->localeLists  = $localeLists;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $locales = $this->getAvailableLocales();
        $_localeLists = $this->localeLists->getOptionLocales();
        $result = [];
        foreach ($locales as $eachStoreLocale) {
            foreach ($_localeLists as $locale) {
                if ($locale['value'] == $eachStoreLocale) {
                    $result[] = [
                        'value' => $locale['value'],
                        'label' => $locale['label']
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * Get OptionsArray
     *
     * @return array
     */
    public function getOptionsArray()
    {
        $options = [];
        foreach ($this->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Get AvailableLocales
     *
     * @return array
     */
    private function getAvailableLocales()
    {
        $locales = [];
        $stores = $this->storeManager->getStores(true, true);
        foreach ($stores as $storeCode => $store) {
            $locale = $this->scopeConfig->getValue(
                DirectoryHelper::XML_PATH_DEFAULT_LOCALE,
                ScopeInterface::SCOPE_STORE,
                $storeCode
            );
            $locales[$storeCode] = $locale;
        }

        return array_unique($locales);
    }
}
