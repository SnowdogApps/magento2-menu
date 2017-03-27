define(function() {
    'use strict'

    const itemParent      = document.querySelectorAll('.menu__item--parent .menu__link'),
          itemInnerParent = document.querySelectorAll('.menu__inner-item--parent > .menu__inner-link'),
          mobileMenu      = document.querySelector('.menu__mobile'),
          navMenu         = document.querySelector('.menu'),
          menuBg          = document.querySelector('.menu__mobile-bg'),
          desktopViewport = window.matchMedia("screen and (min-width: 992px)"),
          menuInnerLists  = document.querySelectorAll('.menu__inner-list');

    function setListHeight(item) {
        return Array.from(item.children)
                    .map(elem => elem.clientHeight)
                    .reduce((a, b) => a + b, 0);
    }

    function findAncestorByClass (el, cls) {
        while ((el = el.parentElement) && !el.classList.contains(cls));
        return el;
    }

    function toggleSubmenu(item, inner) {
        const menuId = item.dataset.menu;
        const menuList = inner
            ? item.parentNode.querySelector(`.menu__inner-list--level2[data-menu="${menuId}"]`)
            : document.querySelector(`.menu__inner-list--level1[data-menu="${menuId}"]`);
        const innerLists = inner
            ? null
            : item.parentNode.querySelectorAll('.menu__inner-list--level2');
        const upperList = inner
            ? findAncestorByClass(menuList, 'menu__inner-list--level1')
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
            let listHeight = setListHeight(menuList);
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
            function(e) {
                if (!desktopViewport.matches) {
                    e.preventDefault();
                    toggleSubmenu(key, true);
                }
            },
            false
        )
    );

    mobileMenu.addEventListener('click', function() {
        navMenu.classList.toggle('open');
    });

    menuBg.addEventListener('click', function() {
        navMenu.classList.toggle('open');
    });

    window.onresize = function() {
        if (desktopViewport.matches) {
            menuInnerLists.forEach(key => key.style.height = 'auto');
            document.querySelectorAll('.menu__list li')
                .forEach(key => key.classList.remove('open'));
            navMenu.classList.remove('open');
        }
        else {
            menuInnerLists.forEach(key => key.style.height = '0');
        }
    }
});
