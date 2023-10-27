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

namespace MageINIC\CityRegionPostcode\Controller\Adminhtml\City;

use MageINIC\CityRegionPostcode\Model\CityFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageINIC\CityRegionPostcode\Api\CityRepositoryInterface as CityRepository;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use MageINIC\CityRegionPostcode\Api\Data\CityInterface;

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
    public const ADMIN_RESOURCE = 'MageINIC_CityRegionPostcode::city_save';
    /**
     * @var CityRepository
     */
    protected CityRepository $cityRepository;
    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;
    /**
     * @var CityFactory
     */
    private CityFactory $cityFactory;

    /**
     * @param Context $context
     * @param CityRepository $cityRepository
     * @param JsonFactory $jsonFactory
     * @param CityFactory $cityFactory
     */
    public function __construct(
        Context $context,
        CityRepository $cityRepository,
        JsonFactory $jsonFactory,
        CityFactory $cityFactory
    ) {
        parent::__construct($context);
        $this->cityRepository = $cityRepository;
        $this->jsonFactory = $jsonFactory;
        $this->cityFactory = $cityFactory;
    }

    /**
     * Process the request
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
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

        foreach (array_keys($postItems) as $cityId) {
            $city = $this->cityFactory->create()->load($cityId);
            try {
                $cityData = $postItems[$cityId];
                $city->setData($cityData);
                $this->cityRepository->save($city);
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithCityId($city, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithCityId(
                    $city,
                    __('Something went wrong while saving the city.')
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
     * @param CityInterface $city
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithCityId(CityInterface $city, string $errorText): string
    {
        return '[Page ID: ' . $city->getId() . '] ' . $errorText;
    }
}
