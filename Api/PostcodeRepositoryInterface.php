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

namespace MageINIC\CityRegionPostcode\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * CityRegionPostcode PostcodeRepositoryInterface
 */
interface PostcodeRepositoryInterface
{
    /**
     * Get Postcode details by City id.
     *
     * @param int $id
     * @return \MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($id);
    /**
     * Save Postcode.
     *
     * @param \MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface $postcode
     * @return \MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\PostcodeInterface $postcode);
    /**
     * Retrieve Postcode matching the specified searchCriteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \MageINIC\CityRegionPostcode\Api\Data\PostcodeSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Postcode.
     *
     * @param \MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface $postcode
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\PostcodeInterface $postcode);
    /**
     * Delete Postcode By Id.
     *
     * @param int $Id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($Id);

    /**
     * Load Postcode details by City id.
     *
     * @param int $city_id
     * @return \MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function loadByCityId(int $city_id);
}
