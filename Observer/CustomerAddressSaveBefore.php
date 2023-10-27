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

namespace MageINIC\CityRegionPostcode\Observer;

use Magento\Customer\Helper\Address as HelperAddress;
use Magento\Framework\Event\Observer;
use Magento\Framework\Registry;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Address;
use Magento\Framework\App\RequestInterface;
use MageINIC\CityRegionPostcode\Api\Data\CityInterface;
use MageINIC\CityRegionPostcode\Api\Data\PostcodeInterface;

/**
 * Customer Observer Model
 */
class CustomerAddressSaveBefore implements ObserverInterface
{
    /**
     * VAT ID validation currently saved address flag
     */
    public const VIV_CURRENTLY_SAVED_ADDRESS = 'currently_saved_address';

    /**
     * @var HelperAddress
     */
    protected HelperAddress $_customerAddress;

    /**
     * @var Registry
     */
    protected Registry $_coreRegistry;

    /**
     * @var RequestInterface
     */
    public RequestInterface $request;

    /**
     * @param HelperAddress $customerAddress
     * @param Registry $coreRegistry
     * @param RequestInterface $request
     */
    public function __construct(
        HelperAddress $customerAddress,
        Registry $coreRegistry,
        RequestInterface $request
    ) {
        $this->_customerAddress = $customerAddress;
        $this->_coreRegistry = $coreRegistry;
        $this->request = $request;
    }

    /**
     * Address before save event handler
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->_coreRegistry->registry(self::VIV_CURRENTLY_SAVED_ADDRESS)) {
            $this->_coreRegistry->unregister(self::VIV_CURRENTLY_SAVED_ADDRESS);
        }
        /** @var $customerAddress Address */
        $customerAddress = $observer->getEvent()->getCustomerAddress();
        if ($this->request->getModuleName() === 'customer'
            && $this->request->getActionName() === 'formPost'
        ) {
            if ($this->request->getParam(CityInterface::ID)) {
                $customerAddress->setCityId($this->request->getParam(CityInterface::ID));
            }
            if ($this->request->getParam(PostcodeInterface::ID)) {
                $customerAddress->setPostcodeId($this->request->getParam(PostcodeInterface::ID));
            }
        }
    }
}
