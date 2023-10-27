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

namespace MageINIC\CityRegionPostcode\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * CityRegionPostcode CityInterface
 */
interface CityInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants
     */
    public const ID           = 'city_id';
    public const COUNTRY_ID   = 'country_id';
    public const REGION_ID    = 'region_id';
    public const CODE         = 'code';
    public const DEFAULT_NAME = 'default_name';
    /**#@-*/

    /**
     * Get City Id
     *
     * @return int
     */
    public function getId();

    /**
     * Set City Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Country Id
     *
     * @return string
     */
    public function getCountryId();

    /**
     * Set Country Id
     *
     * @param string $countryId
     * @return $this
     */
    public function setCountryId($countryId);

    /**
     * Get Region Id
     *
     * @return int /null
     */
    public function getRegionId();

    /**
     * Set Region Id
     *
     * @param int $regionId
     * @return $this
     */
    public function setRegionId($regionId);

    /**
     * Get City Code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set City Code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * Get City Default Name
     *
     * @return string
     */
    public function getDefaultName();

    /**
     * Set City Default Name
     *
     * @param string $defaultName
     * @return $this
     */
    public function setDefaultName($defaultName);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \MageINIC\CityRegionPostcode\Api\Data\CityExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \MageINIC\CityRegionPostcode\Api\Data\CityExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \MageINIC\CityRegionPostcode\Api\Data\CityExtensionInterface $extensionAttributes
    );
}
