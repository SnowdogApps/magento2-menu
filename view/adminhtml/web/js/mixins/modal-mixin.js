define([
  'Magento_Ui/js/lib/view/utils/async',
  'Magento_Ui/js/modal/alert',
  'uiRegistry'
], function ($, alert, registry) {
  'use strict'

  return function (modal) {
    return modal.extend({
      actionImport: function () {
        let importModal = this

        alert({
          title: $.mage.__('Attention'),
          content: $.mage.__(
            'You are attempting to recreate the menu nodes, all your current nodes will be removed and new nodes based in category tree will be created. Are you sure?'
          ),
          buttons: [
            {
              text: $.mage.__('Cancel'),
              class: 'action-secondary action-dismiss',

              click: function () {
                this.closeModal(true)
              }
            },
            {
              text: $.mage.__('OK'),
              class: 'action-primary action-accept',

              click: function () {
                let alertModal = this,
                  form = registry.get('snowmenu_menu_form.data_source'),
                  vueApp = registry.get('vueApp'),
                  categoryId = form.data.category_id,
                  depth = form.data.depth

                $.ajax({
                  showLoader: true,
                  url: '/admin/snowmenu/menu/importCategories',
                  data: {
                    category_id: categoryId,
                    depth: depth
                  },
                  type: 'POST',
                  dataType: 'json',
                  success: function (data) {
                    vueApp.list = data.list

                    alertModal.closeModal(true)
                    importModal.closeModal()
                  },
                  error: function (result) {
                    alertModal.closeModal(true)
                  }
                })
              }
            }
          ]
        })
      }
    })
  }
})
