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

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageINIC\CityRegionPostcode\Api\Data;
use MageINIC\CityRegionPostcode\Api\Data\PostcodeInterfaceFactory;
use MageINIC\CityRegionPostcode\Model\ResourceModel\Postcode as PostcodeResource;
use MageINIC\CityRegionPostcode\Api\PostcodeRepositoryInterface;
use Magento\Framework\Reflection\DataObjectProcessor;
use MageINIC\CityRegionPostcode\Model\ResourceModel\Postcode\CollectionFactory;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * CityRegionPostcode PostcodeRepository Class
 */
class PostcodeRepository implements PostcodeRepositoryInterface
{
    /**
     * @var PostcodeResource
     */
    private PostcodeResource $postcodeResource;
    /**
     * @var CollectionFactory
     */
    private CollectionFactory $postcodeCollectionFactory;
    /**
     * @var Data\CitySearchResultsInterfaceFactory
     */
    protected Data\CitySearchResultsInterfaceFactory $searchResultsFactory;
    /**
     * @var JoinProcessorInterface
     */
    private JoinProcessorInterface $extensionJoinProcessor;
    /**
     * @var CollectionProcessorInterface
     */
    private CollectionProcessorInterface $collectionProcessor;
    /**
     * @var PostcodeInterfaceFactory
     */
    private PostcodeInterfaceFactory $postcodeInterfaceFactory;
    /**
     * @var PostcodeFactory
     */
    private PostcodeFactory $postcodeFactory;

    /**
     * @param PostcodeResource $postcodeResource
     * @param PostcodeFactory $postcodeFactory
     * @param CollectionFactory $postcodeCollectionFactory
     * @param Data\CitySearchResultsInterfaceFactory $searchResultsFactory
     * @param JoinProcessorInterface $extensionJoinProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @param PostcodeInterfaceFactory $postcodeInterfaceFactory
     */
    public function __construct(
        PostcodeResource $postcodeResource,
        PostcodeFactory $postcodeFactory,
        CollectionFactory $postcodeCollectionFactory,
        Data\CitySearchResultsInterfaceFactory $searchResultsFactory,
        JoinProcessorInterface $extensionJoinProcessor,
        CollectionProcessorInterface $collectionProcessor,
        PostcodeInterfaceFactory $postcodeInterfaceFactory
    ) {
        $this->postcodeCollectionFactory = $postcodeCollectionFactory;
        $this->postcodeFactory           = $postcodeFactory;
        $this->searchResultsFactory      = $searchResultsFactory;
        $this->postcodeResource          = $postcodeResource;
        $this->extensionJoinProcessor    = $extensionJoinProcessor;
        $this->collectionProcessor       = $collectionProcessor;
        $this->postcodeInterfaceFactory  = $postcodeInterfaceFactory;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        $postcode = $this->postcodeFactory->create();
        $this->postcodeResource->load($postcode, $id);
        if (!$postcode->getId()) {
            throw new NoSuchEntityException(
                __('Unable to find the record with postcode id %1', $id)
            );
        }
        return $postcode;
    }

    /**
     * @inheritdoc
     */
    public function save(Data\PostcodeInterface $postcode)
    {
        try {
            $this->postcodeResource->save($postcode);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the city data: %1', $exception->getMessage())
            );
        }
        return $postcode;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->postcodeCollectionFactory->create();
        $this->extensionJoinProcessor->process($collection);
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->postcodeInterfaceFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function delete(Data\PostcodeInterface $postcode)
    {
        try {
            $this->postcodeResource->delete($postcode);
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
    public function loadByCityId(int $city_id)
    {
        $collection = $this->postcodeCollectionFactory->create();
        $postcode = $collection->addFieldToFilter('city_id', ['eq' => $city_id])->getData();

        if (!$postcode) {
            throw new NoSuchEntityException(
                __('Unable to find the record with postcode id %1', $city_id)
            );
        }
        return $postcode;
    }
}
