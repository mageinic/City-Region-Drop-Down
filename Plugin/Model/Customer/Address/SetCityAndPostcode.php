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

namespace MageINIC\CityRegionPostcode\Plugin\Model\Customer\Address;

use MageINIC\CityRegionPostcode\Model\City;
use MageINIC\CityRegionPostcode\Model\Postcode;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Model\Address;
use MageINIC\CityRegionPostcode\Model\CityFactory;
use MageINIC\CityRegionPostcode\Model\PostcodeFactory;

/**
 * CityRegionPostcode Customer Address SetCityAndPostcode Class
 */
class SetCityAndPostcode
{
    /**
     * @var array
     */
    protected static array $_cityModels = [];
    /**
     * @var array
     */
    protected static array $postcode = [];
    /**
     * @var CityFactory
     */
    protected CityFactory $cityFactory;
    /**
     * @var PostcodeFactory
     */
    private PostcodeFactory $postcodeFactory;

    /**
     * @param CityFactory $cityFactory
     * @param PostcodeFactory $postcodeFactory
     */
    public function __construct(
        CityFactory $cityFactory,
        PostcodeFactory $postcodeFactory
    ) {
        $this->cityFactory     = $cityFactory;
        $this->postcodeFactory = $postcodeFactory;
    }

    /**
     * Update city name as per locale
     *
     * @param Address $subject
     * @param AddressInterface $addressDataObject
     * @return mixed
     */
    public function afterGetDataModel(
        Address $subject,
        AddressInterface $addressDataObject
    ) {
        $addressDataObject->setCity(
            $this->getCity($addressDataObject->getCity(), $subject->getCityId())
        );
        $addressDataObject->setPostcode(
            $this->getPostcode($addressDataObject->getPostcode(), $subject->getPostcodeId())
        );
        return $addressDataObject;
    }

    /**
     * Get city
     *
     * @param string $city
     * @param int $cityId
     * @return mixed|string
     */
    public function getCity(string $city, $cityId)
    {
        if ($cityId) {
            $city = $this->getCityModel($cityId)->getName();
        }

        return $city;
    }

    /**
     * Get cityModel
     *
     * @param int $cityId
     * @return City
     */
    public function getCityModel(int $cityId)
    {
        if (! isset(self::$_cityModels[$cityId])) {
            $city = $this->cityFactory->create()->load($cityId);
            self::$_cityModels[$cityId] = $city;
        }

        return self::$_cityModels[$cityId];
    }

    /**
     * Get postcode
     *
     * @param string $postcode
     * @param int $postcodeId
     * @return mixed|string
     */
    public function getPostcode(string $postcode, $postcodeId)
    {
        if ($postcodeId) {
            $postcode = $this->getPostcodeModel($postcodeId)->getPostcode();
        }

        return $postcode;
    }

    /**
     * Get postcodeModel
     *
     * @param int $postcodeId
     * @return Postcode
     */
    public function getPostcodeModel(int $postcodeId)
    {
        if (! isset(self::$postcode[$postcodeId])) {
            $postcodeData = $this->postcodeFactory->create()->load($postcodeId);
            self::$postcode[$postcodeId] = $postcodeData;
        }

        return self::$postcode[$postcodeId];
    }
}
