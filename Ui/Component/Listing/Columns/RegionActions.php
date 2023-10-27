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

namespace MageINIC\CityRegionPostcode\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * CityRegionPostcode RegionActions Class
 */
class RegionActions extends Column
{
    public const EDIT_PAGE_ROUTE   = 'cityregionpostcode/region/edit';
    public const DELETE_PAGE_ROUTE = 'cityregionpostcode/region/delete';

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlInterface;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlInterface
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlInterface,
        array $components = [],
        array $data = []
    ) {
        $this->urlInterface = $urlInterface;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare DataSource
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item['region_id'])) {
                    $item[$name]['edit'] = [
                        'href'  => $this->urlInterface->getUrl(
                            self::EDIT_PAGE_ROUTE,
                            ['region_id' => $item['region_id']]
                        ),
                        'label' => __('Edit')
                    ];

                    $item[$name]['delete'] = [
                        'href'      => $this->urlInterface->getUrl(
                            self::DELETE_PAGE_ROUTE,
                            ['region_id' => $item['region_id']]
                        ),
                        'label'     => __('Delete'),
                        'confirm'   => [
                            'title'     => __('Delete '),
                            'message'   => __('Are you sure you want to delete a record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
