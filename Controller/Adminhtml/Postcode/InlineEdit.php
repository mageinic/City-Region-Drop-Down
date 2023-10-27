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

namespace MageINIC\CityRegionPostcode\Controller\Adminhtml\Postcode;

use MageINIC\CityRegionPostcode\Model\PostcodeFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageINIC\CityRegionPostcode\Api\PostcodeRepositoryInterface as PostcodeRepository;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * CityRegionPostcode city grid inline edit controller
 */
class InlineEdit extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'MageINIC_CityRegionPostcode::postcode_save';
    /**
     * @var PostcodeRepository
     */
    protected PostcodeRepository $postcodeRepository;
    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;
    /**
     * @var PostcodeFactory
     */
    private PostcodeFactory $postcodeFactory;

    /**
     * @param Context $context
     * @param PostcodeRepository $postcodeRepository
     * @param JsonFactory $jsonFactory
     * @param PostcodeFactory $postcodeFactory
     */
    public function __construct(
        Context $context,
        PostcodeRepository $postcodeRepository,
        JsonFactory $jsonFactory,
        PostcodeFactory $postcodeFactory
    ) {
        parent::__construct($context);
        $this->postcodeRepository = $postcodeRepository;
        $this->jsonFactory = $jsonFactory;
        $this->postcodeFactory = $postcodeFactory;
    }

    /**
     * Process the request
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                    'messages' => [__('Please correct the data sent.')],
                    'error' => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $postcodeId) {
            $postcode = $this->postcodeFactory->create()->load($postcodeId);
            try {
                $postcodeData = $postItems[$postcodeId];
                $postcode->setData($postcodeData);
                $this->postcodeRepository->save($postcode);
            } catch (LocalizedException $e) {
                $messages[] = $this->getErrorWithPostcodeId($postcode, $e->getMessage());
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPostcodeId($postcode, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPostcodeId(
                    $postcode,
                    __('Something went wrong while saving the postcode.')
                );
                $error = true;
            }
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error' => $error
            ]
        );
    }

    /**
     * Add page title to error message
     *
     * @param PostcodeInterface $postcode
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithPostcodeId(PostcodeInterface $postcode, string $errorText): string
    {
        return '[Page ID: ' . $postcode->getId() . '] ' . $errorText;
    }
}
