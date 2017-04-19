define(function() {
    'use strict'

    return function(options) {
        const menuMainClass   = options.menuClass,
              alliTemLabel    = options.allLabel,
              itemParent      = document.querySelectorAll(`.${menuMainClass}__item--parent > .${menuMainClass}__link`),
              itemInnerParent = document.querySelectorAll(`.${menuMainClass}__inner-item--parent > .${menuMainClass}__inner-link`),
              mobileMenu      = document.querySelector(`.${menuMainClass}__mobile`),
              navMenu         = document.querySelector(`.${menuMainClass}`),
              menuBg          = document.querySelector(`.${menuMainClass}__mobile-bg`),
              desktopViewport = window.matchMedia('screen and (min-width: 992px)'),
              menuInnerLists  = document.querySelectorAll(`.${menuMainClass}__inner-list`),
              documentBody    = document.querySelector('body');


        function setListHeight(item) {
            return Array.from(item.children)
                        .map(elem => elem.clientHeight)
                        .reduce((a, b) => a + b, 0);
        }

        function toggleSubmenu(item, inner) {
            const menuId     = item.dataset.menu,
                  menuList   = inner
                  ? item.parentNode.querySelector(`.${menuMainClass}__inner-list--level2[data-menu="${menuId}"]`)
                  : document.querySelector(`.${menuMainClass}__inner-list--level1[data-menu="${menuId}"]`),
                  innerLists = inner
                  ? null
                  : item.parentNode.querySelectorAll(`.${menuMainClass}__inner-list--level2`),
                  upperList  = inner
                  ? item.closest(`.${menuMainClass}__inner-list--level1`)
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
                (e) => {
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
            // disable document scrolling while menu is open
            documentBody.classList.toggle('menu-open');
        });

        menuBg.addEventListener('click', () => {
            navMenu.classList.toggle('open');
            // disable document scrolling while menu is open
            documentBody.classList.toggle('menu-open');
        });

        window.addEventListener('resize', () => {
            if (desktopViewport.matches) {
                menuInnerLists.forEach(key => key.style.height = 'auto');
                document.querySelectorAll(`.${menuMainClass}__list li`)
                        .forEach(key => key.classList.remove('open'));
                navMenu.classList.remove('open');
            }
            else {
                menuInnerLists.forEach(key => key.style.height = '0');
            }
        });
    }
});
