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

namespace MageINIC\CityRegionPostcode\Ui\Model;

use Magento\Directory\Model\ResourceModel\Region\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City as CityResource;
use Magento\Directory\Model\ResourceModel\Region\Collection;

/**
 * CityRegionPostcode RegionDataProvider Class
 */
class RegionDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected Collection $collectionFactory;

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var array
     */
    protected array $loadedData;
    /**
     * @var CityResource
     */
    protected CityResource $cityResource;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param CityResource $cityResource
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        CityResource $cityResource,
        array $meta = [],
        array $data = []
    ) {
        $this->collection    = $collectionFactory->create()->load();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
        $this->cityResource = $cityResource;
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();
        foreach ($items as $region) {
            $this->loadedData[$region->getId()] = $region->getData();
            $localesName = $this->cityResource->getRegionLocales($region->getId());
            if (! empty($localesName)) {
                $this->loadedData[$region->getId()]['region_locales'] = $localesName;
            }
        }

        $data = $this->dataPersistor->get('mageinic_cityregionpostcode_region');
        if (! empty($data)) {
            $region = $this->collection->getNewEmptyItem();
            $region->setData($data);
            $this->loadedData[$region->getId()] = $region->getData();
            $this->dataPersistor->clear('mageinic_cityregionpostcode_region');
        }

        return $this->loadedData;
    }
}
