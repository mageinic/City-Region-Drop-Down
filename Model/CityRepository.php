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

namespace MageINIC\CityRegionPostcode\Model;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageINIC\CityRegionPostcode\Api\CityRepositoryInterface;
use MageINIC\CityRegionPostcode\Api\Data;
use MageINIC\CityRegionPostcode\Api\Data\CitySearchResultsInterfaceFactory;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City as CityResource;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City\Collection;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City\CollectionFactory;

/**
 * CityRegionPostcode CityRepository Class
 */
class CityRepository implements CityRepositoryInterface
{
    /**
     * @var CityResource
     */
    private CityResource $cityResource;
    /**,
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;
    /**
     * @var CitySearchResultsInterfaceFactory
     */
    private CitySearchResultsInterfaceFactory $citySearchResultsInterfaceFactory;
    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;
    /**
     * @var Data\CitySearchResultsInterfaceFactory
     */
    protected CitySearchResultsInterfaceFactory $searchResultsFactory;
    /**
     * @var JoinProcessorInterface
     */
    private JoinProcessorInterface $joinProcessor;
    /**
     * @var CityFactory
     */
    private CityFactory $cityFactory;

    /**
     * @param CityResource $cityResource
     * @param CityFactory $cityFactory
     * @param CollectionFactory $collectionFactory
     * @param CitySearchResultsInterfaceFactory $citySearchResultsInterfaceFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param CitySearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $joinProcessor
     */
    public function __construct(
        CityResource $cityResource,
        CityFactory $cityFactory,
        CollectionFactory $collectionFactory,
        CitySearchResultsInterfaceFactory $citySearchResultsInterfaceFactory,
        CollectionProcessorInterface $collectionProcessor,
        Data\CitySearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $joinProcessor
    ) {
        $this->cityResource                      = $cityResource;
        $this->cityFactory                       = $cityFactory;
        $this->collectionFactory                 = $collectionFactory;
        $this->citySearchResultsInterfaceFactory = $citySearchResultsInterfaceFactory;
        $this->collectionProcessor               = $collectionProcessor;
        $this->searchResultsFactory              = $searchResultsFactory;
        $this->joinProcessor                     = $joinProcessor;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {

        $city = $this->cityFactory->create();
        $this->cityResource->load($city, $id);
        if (!$city->getId()) {
            throw new NoSuchEntityException(
                __('Unable to find the record with city id %1', $id)
            );
        }
        return $city;
    }

    /**
     * @inheritdoc
     */
    public function save(Data\CityInterface $city)
    {
        try {
            $this->cityResource->save($city);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the city data: %1', $exception->getMessage())
            );
        }
        return $city;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->joinProcessor->process($collection);
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->citySearchResultsInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(Data\CityInterface $city)
    {
        try {
            $this->cityResource->delete($city);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __('Could not delete the city: %1', $exception->getMessage())
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @inheritdoc
     */
    public function loadByRegionId(int $region_id)
    {
        $collectionCity = $this->collectionFactory->create();
        $city = $collectionCity->addFieldToFilter('region_id', ['eq' => $region_id])->getData();
        if (!$city) {
            throw new NoSuchEntityException(
                __('Unable to find the record with city id %1', $region_id)
            );
        }
        return $city;
    }
}
