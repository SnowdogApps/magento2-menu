define(function() {
    'use strict'

    return function(options) {
        const menuMainclass = options.menuClass,
              itemParent      = document.querySelectorAll(`.${menuMainclass}__item--parent .menu__link`),
              itemInnerParent = document.querySelectorAll(`.${menuMainclass}__inner-item--parent > .${menuMainclass}__inner-link`),
              mobileMenu      = document.querySelector(`.${menuMainclass}__mobile`),
              navMenu         = document.querySelector(`.${menuMainclass}`),
              menuBg          = document.querySelector(`.${menuMainclass}__mobile-bg`),
              desktopViewport = window.matchMedia('screen and (min-width: 992px)'),
              menuInnerLists  = document.querySelectorAll(`.${menuMainclass}__inner-list`);

        function setListHeight(item) {
            return Array.from(item.children)
                        .map(elem => elem.clientHeight)
                        .reduce((a, b) => a + b, 0);
        }

        function toggleSubmenu(item, inner) {
            const menuId = item.dataset.menu,
                  menuList = inner
                  ? item.parentNode.querySelector(`.${menuMainclass}__inner-list--level2[data-menu="${menuId}"]`)
                  : document.querySelector(`.${menuMainclass}__inner-list--level1[data-menu="${menuId}"]`),
                  innerLists = inner
                  ? null
                  : item.parentNode.querySelectorAll(`.${menuMainclass}__inner-list--level2`),
                  upperList = inner
                  ? item.closest(`.${menuMainclass}__inner-list--level1`)
                  : null;

            item.parentNode.classList.toggle('open');

            if (menuList && menuList.clientHeight > 0) {
                if (innerLists) {
                    innerLists.forEach(key => {
                        key.style.height = 0;
                        key.parentNode.classList.remove('open');
                    });
                }
                else if (upperList) {
                    upperList.style.height = upperList.clientHeight - menuList.clientHeight + 'px';
                }
                menuList.style.height = 0;
            }
            else {
                const listHeight = setListHeight(menuList);
                menuList.style.height = listHeight + 'px';

                if (upperList) {
                    upperList.style.height = upperList.clientHeight + listHeight + 'px';
                }
            }
        }

        itemParent.forEach(
            key => key.addEventListener(
                'click',
                function(e) {
                    if (!desktopViewport.matches) {
                        e.preventDefault();
                        toggleSubmenu(key, false);
                    }
                },
                false
            )
        );

        itemInnerParent.forEach(
            key => key.addEventListener(
                'click',
                (e) => {
                    if (!desktopViewport.matches) {
                        e.preventDefault();
                        toggleSubmenu(key, true);
                    }
                },
                false
            )
        );

        mobileMenu.addEventListener('click', () => {
            navMenu.classList.toggle('open');
        });

        menuBg.addEventListener('click', () => {
            navMenu.classList.toggle('open');
        });

        window.onresize = () => {
            if (desktopViewport.matches) {
                menuInnerLists.forEach(key => key.style.height = 'auto');
                document.querySelectorAll(`.${menuMainclass}__list li`)
                        .forEach(key => key.classList.remove('open'));
                navMenu.classList.remove('open');
            }
            else {
                menuInnerLists.forEach(key => key.style.height = '0');
            }
        }
    }
});
