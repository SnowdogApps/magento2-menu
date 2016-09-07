<?php

namespace Snowdog\Menu\Block\Menu\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Snowdog\Menu\Controller\Adminhtml\Menu\Edit;

class Form extends Generic
{
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post']]
        );
        $form->setUseContainer(true);

        $menu = $this->_coreRegistry->registry(Edit::REGISTRY_CODE);
        if ($menu) {
            $form->addField('menu_id', 'hidden', ['name' => 'id']);
        }

        $this->setForm($form);
        return $this;
    }

    protected function _initFormValues()
    {
        $menu = $this->_coreRegistry->registry(Edit::REGISTRY_CODE);

        if ($menu) {
            $this->getForm()->setValues($menu->getData());
        }
        return $this;
    }


}