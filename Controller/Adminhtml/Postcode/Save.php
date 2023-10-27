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

namespace MageINIC\CityRegionPostcode\Controller\Adminhtml\Postcode;

use MageINIC\CityRegionPostcode\Model\PostcodeRepository;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use MageINIC\CityRegionPostcode\Api\PostcodeRepositoryInterface;
use MageINIC\CityRegionPostcode\Model\PostcodeFactory;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use MageINIC\CityRegionPostcode\Model\Config\Source\Locale;

/**
 * Save CityRegionPostcode postcode action.
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
    public const ADMIN_RESOURCE = 'MageINIC_CityRegionPostcode::postcode_save';
    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var PostcodeFactory
     */
    private PostcodeFactory $postcodeFactory;

    /**
     * @var PostcodeRepositoryInterface
     */
    private PostcodeRepositoryInterface $postcodeRepository;
    /**
     * @var Locale
     */
    private Locale $locale;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param Locale $locale
     * @param PostcodeFactory|null $postcodeFactory
     * @param PostcodeRepositoryInterface|null $postcodeRepository
     */
    public function __construct(
        Action\Context              $context,
        DataPersistorInterface      $dataPersistor,
        Locale                      $locale,
        PostcodeFactory             $postcodeFactory = null,
        PostcodeRepositoryInterface $postcodeRepository = null
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->postcodeFactory = $postcodeFactory ?:
            ObjectManager::getInstance()->get(PostcodeFactory::class);
        $this->postcodeRepository = $postcodeRepository ?:
            ObjectManager::getInstance()->get(PostcodeRepositoryInterface::class);
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
            if (empty($data['postcode_id'])) {
                $data['postcode_id'] = null;
            }

            $model = $this->postcodeFactory->create();

            $id = $this->getRequest()->getParam('postcode_id');
            if ($id) {
                try {
                    $model = $this->postcodeFactory->create()->load($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This postcode no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }
            $model->addData($data);

            try {
                $this->_eventManager->dispatch(
                    'postcode_postcode_prepare_save',
                    ['postcode' => $model, 'request' => $this->getRequest()]
                );
                $this->postcodeRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the postcode.'));
                $this->dataPersistor->clear('postcode_postcode');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        ['postcode_id' => $model->getId(), '_current' => true]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the postcode.'));
            }
            $this->dataPersistor->set('postcode_postcode', $data);
            return $resultRedirect->setPath(
                '*/*/edit',
                ['postcode_id' => $this->getRequest()->getParam('postcode_id')]
            );
        }
        return $resultRedirect->setPath('*/*/');
    }
}
