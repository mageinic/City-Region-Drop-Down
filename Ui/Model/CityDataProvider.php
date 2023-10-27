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

use Magento\Ui\DataProvider\AbstractDataProvider;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City\Collection;

/**
 * CityRegionPostcode CityDataProvider Class
 */
class CityDataProvider extends AbstractDataProvider
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
    protected $loadedData;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create()->load();
        $this->dataPersistor = $dataPersistor;
        $this->meta = $this->prepareMeta($this->meta);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        foreach ($items as $city) {
            $this->loadedData[$city->getId()] = $city->getData();
            $localeNames = $city->getLocaleNames();
            if (! empty($localeNames)) {
                $this->loadedData[$city->getId()]['city_locales'] = $city->getLocaleNames();
            }
        }

        $data = $this->dataPersistor->get('mageinic_cityregionpostcode_city');

        if (! empty($data)) {
            $city = $this->collection->getNewEmptyItem();
            $city->setData($data);
            $this->loadedData[$city->getId()] = $city->getData();
            $this->dataPersistor->clear('mageinic_cityregionpostcode_city');
        }
        return $this->loadedData;
    }
}
