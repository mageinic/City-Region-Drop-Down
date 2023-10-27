<?php
declare(strict_types=1);
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

namespace MageINIC\CityRegionPostcode\Controller\Adminhtml\City;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use MageINIC\CityRegionPostcode\Api\CityRepositoryInterface;
use MageINIC\CityRegionPostcode\Model\CityFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageINIC\CityRegionPostcode\Model\Config\Source\Locale;

/**
 * Save CityRegionPostcode city action.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action implements HttpPostActionInterface
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_CityRegionPostcode::city_save';
    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var CityFactory
     */
    private CityFactory $cityFactory;

    /**
     * @var CityRepositoryInterface
     */
    private CityRepositoryInterface $cityRepository;
    /**
     * @var Locale
     */
    private Locale $locale;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param Locale $locale
     * @param CityFactory|null $cityFactory
     * @param CityRepositoryInterface|null $cityRepository
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        Locale $locale,
        CityFactory $cityFactory = null,
        CityRepositoryInterface $cityRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->cityFactory = $cityFactory ?: ObjectManager::getInstance()->get(CityFactory::class);
        $this->cityRepository = $cityRepository ?: ObjectManager::getInstance()->get(CityRepositoryInterface::class);
        parent::__construct($context);
        $this->locale = $locale;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (empty($data['city_id'])) {
                $data['city_id'] = null;
            }

            $model = $this->cityFactory->create();

            $id = $this->getRequest()->getParam('city_id');
            if ($id) {
                try {
                    $model = $this->cityFactory->create()->load($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This city no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $locales = [];
            if (array_key_exists('city_locales', $data)) {
                $locales = $data['city_locales'];
                unset($data['city_locales']);
            }
            $model->addData($data);

            try {
                $this->_eventManager->dispatch(
                    'city_city_prepare_save',
                    ['city' => $model, 'request' => $this->getRequest()]
                );
                $locales = $this->prepareCityLocales($locales);
                $model->setPostLocaleNames($locales['result']);
                $this->cityRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the city.'));
                $this->dataPersistor->clear('city_city');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['city_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the city.'));
            }

            $data['city_locales'] = $locales['locale_data_persistor'];
            $this->dataPersistor->set('mageinic_cityregionrostcode_city', $data);
            $this->dataPersistor->set('city_city', $data);
            return $resultRedirect->setPath('*/*/edit', ['city_id' => $this->getRequest()->getParam('city_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Prepare city locale's
     *
     * @param array $locales
     * @return array
     */
    private function prepareCityLocales($locales)
    {
        $result = [];
        if (is_array($locales) && ! empty($locales)) {
            $result = array_filter($locales, function ($localeName) {
                return strlen($localeName);
            });
        }

        $allStoreLocales = $this->locale->toOptionArray();
        $localeDataPersistor = [];
        foreach ($allStoreLocales as $store) {
            if (array_key_exists($store['value'], $result)) {
                $localeDataPersistor[$store['value']] = $result[$store['value']];
            } else {
                $localeDataPersistor[$store['value']] = $store['label'];
            }
        }

        return [
            'locale_data_persistor' => $localeDataPersistor,
            'result' => $result
        ];
    }
}
