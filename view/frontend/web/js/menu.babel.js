define([], function () {
  'use strict';

  class Menu {
    constructor(element) {
      this.menu = element;
      this.links = this.menu.querySelectorAll('a');
      this.path = window.location.href;

      if (this.links.length) {
        this.setCurrentItem();
      }
    }

    setCurrentItem() {
      this.links.forEach((link) => {
        if (this.path === link.getAttribute('href')) {
          link.classList.add('current');
        }
      });
    }
  }

  return function (config, element) {
    new Menu(element);
  };
});
