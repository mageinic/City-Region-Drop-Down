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

declare(strict_types=1);

namespace MageINIC\CityRegionPostcode\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use MageINIC\CityRegionPostcode\Api\CityRepositoryInterface;
use MageINIC\CityRegionPostcode\Model\RegionFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageINIC\CityRegionPostcode\Model\ResourceModel\City as CityResourceModel;

/**
 * Save CityRegionPostcode Region action.
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
    public const ADMIN_RESOURCE = 'MageINIC_CityRegionPostcode::region_save';
    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var RegionFactory
     */
    private RegionFactory $regionFactory;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param RegionFactory|null $regionFactory
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        RegionFactory $regionFactory = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->regionFactory = $regionFactory ?: ObjectManager::getInstance()->get(RegionFactory::class);
        parent::__construct($context);
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

            $model = $this->regionFactory->create();

            $id = $this->getRequest()->getParam('city_id');
            if ($id) {
                try {
                    $model = $this->regionFactory->create()->load($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This city no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $model->addData($data);

            try {
                $this->_eventManager->dispatch(
                    'region_region_prepare_save',
                    ['city' => $model, 'request' => $this->getRequest()]
                );
                $region = $model->save();
                if (array_key_exists('region_locales', $data) && is_array($data['region_locales'])) {
                    $cityResourceModel = $this->_objectManager->create(CityResourceModel::class);
                    $cityResourceModel->saveRegionLocales($region->getId(), $data['region_locales']);
                }
                $this->messageManager->addSuccessMessage(__('You saved the region.'));
                $this->dataPersistor->clear('region_region');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['region_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the region.'));
            }
            $this->dataPersistor->set('region_region', $data);
            return $resultRedirect->setPath('*/*/edit', ['region_id' => $this->getRequest()->getParam('region_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
