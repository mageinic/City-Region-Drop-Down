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

namespace MageINIC\CityRegionPostcode\Plugin\Model\Sales\AdminOrder;

use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\AdminOrder\Create as CoreCreate;

/**
 * CityRegionPostcode Sales AdminOrder Create Class
 */
class Create
{
    /**
     * @var RequestInterface
     */
    private RequestInterface $request;

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * After Quote saving
     *
     * @param CoreCreate $subject
     * @param $result
     * @return mixed
     */
    public function afterSaveQuote(
        CoreCreate $subject,
        $result
    ) {
        if ($subject->getQuote()->getId()) {
            $post = $this->request->getPost('order');
            $quote = $subject->getQuote();
            $shippingAddress = $quote->getShippingAddress();
            $billingAddress = $quote->getBillingAddress();

            if ($billingAddress && $billingAddress->getId() && isset($post['billing_address'])) {
                if (isset($post['billing_address']['city_id'])) {
                    $billingAddress->setCityId($post['billing_address']['city_id']);
                }
            }
            if ($billingAddress && $billingAddress->getId() && isset($post['billing_address'])) {
                if (isset($post['billing_address']['postcode_id'])) {
                    $billingAddress->setCityId($post['billing_address']['postcode_id']);
                }
            }
            if (! $quote->isVirtual() && $shippingAddress && $shippingAddress->getId()) {
                if (isset($post['billing_address'])
                    && isset($post['billing_address']['city_id'])
                    && ($shippingAddress->getSameAsBilling() || $shippingAddress->getSameAsBilling() == 1)
                ) {
                    $shippingAddress->setCityId($post['billing_address']['city_id']);
                } elseif (isset($post['shipping_address'])
                    && isset($post['shipping_address']['city_id'])
                ) {
                    $shippingAddress->setCityId($post['shipping_address']['city_id']);
                }
            }

            if (! $quote->isVirtual() && $shippingAddress && $shippingAddress->getId()) {
                if (isset($post['billing_address'])
                    && isset($post['billing_address']['postcode_id'])
                    && ($shippingAddress->getSameAsBilling() || $shippingAddress->getSameAsBilling() == 1)
                ) {
                    $shippingAddress->setCityId($post['billing_address']['postcode_id']);
                } elseif (isset($post['shipping_address'])
                    && isset($post['shipping_address']['postcode_id'])
                ) {
                    $shippingAddress->setCityId($post['shipping_address']['postcode_id']);
                }
            }
        }
        return $result;
    }
}
