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

namespace MageINIC\CityRegionPostcode\Block\Adminhtml\Customer\Address\Edit;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\LayoutInterface;
use MageINIC\CityRegionPostcode\Helper\Data as HelperData;
use MageINIC\CityRegionPostcode\ViewModel\CityViewModel;
use MageINIC\CityRegionPostcode\ViewModel\PostCodeViewModel;

/**
 * CityRegionPostcode Customer Address Edit Js Class
 */
class Js extends Template
{
    public const CUSTOMER_ADDRESS_EDIT_BLOCK_NAME = 'customer_address_edit';

    /**
     * @var LayoutInterface
     */
    private LayoutInterface $currentLayout;

    /**
     * @var helperData
     */
    private HelperData $helperData;
    /**
     * @var CityViewModel
     */
    private CityViewModel $cityViewModel;
    /**
     * @var PostCodeViewModel
     */
    private PostCodeViewModel $postCodeViewModel;

    /**
     * @param Context $context
     * @param HelperData $helperData
     * @param CityViewModel $cityViewModel
     * @param PostCodeViewModel $postCodeViewModel
     * @param array $data
     */
    public function __construct(
        Context           $context,
        HelperData        $helperData,
        CityViewModel     $cityViewModel,
        PostCodeViewModel $postCodeViewModel,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData        = $helperData;
        $this->currentLayout     = $context->getLayout();
        $this->cityViewModel     = $cityViewModel;
        $this->postCodeViewModel = $postCodeViewModel;
    }

    /**
     * This method should not be overridden. You can override _toHtml() method in descendants if needed.
     *
     * @return string
     */
    public function toHtml()
    {
        if (!$this->helperData->isActive()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Is Active
     *
     * @return mixed
     */
    public function isActive()
    {
        return $this->helperData->isActive();
    }

    /**
     * Get CitySerializeValues
     *
     * @return bool|string
     */
    public function getCitySerializeValues()
    {
        return $this->cityViewModel->getCitySerializeValues();
    }

    /**
     * Get PostCodeSerializeValues
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostCodeSerializeValues()
    {
        return $this->postCodeViewModel->getPostCodeSerializeValues();
    }
}
