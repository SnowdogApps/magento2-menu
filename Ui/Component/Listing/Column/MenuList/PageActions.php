<?php
namespace Snowdog\Menu\Ui\Component\Listing\Column\MenuList;

use Magento\Ui\Component\Listing\Columns\Column;

class PageActions extends Column
{
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as & $item) {
                $name = $this->getData("name");
                $id = $item["menu_id"] ?? "X";
                $item[$name]["view"] = [
                    "href"  => $this->getContext()->getUrl(
                        "snowmenu/menu/edit",
                        ["id" => $id]
                    ),
                    "label" => __("Edit"),
                ];

                $isActive = (int) $item['is_active'] === 1;
                $item[$name]['change_status'] = [
                    'href' => $this->getContext()->getUrl(
                        'snowmenu/menu/status',
                        ['id' => $id, 'is_active' => !$isActive]
                    ),
                    'label' => __($isActive ? 'Disable' : 'Enable'),
                ];
            }
        }

        return $dataSource;
    }
}
