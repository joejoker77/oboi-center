import './bootstrap';
import {
    Autoplay,
    EffectCoverflow,
    FreeMode,
    Mousewheel,
    Navigation,
    Pagination,
    Scrollbar,
    Swiper,
    Thumbs
} from "swiper";
import videojs from "video.js";

import IMask from "imask";

import * as noUiSlider from 'nouislider/dist/nouislider.min';
import swipe from "bootstrap/js/src/util/swipe";


const buttonGetProfileForm = document.getElementById('getFormProfile'),
    modal                  = document.getElementById('mainModal'),
    modalWindow            = new Modal(modal),
    carousel               = document.querySelector('.carousel'),
    megaMenu               = document.getElementById('mainMenu'),
    bannerItems            = document.querySelectorAll('.banner-container .category-item'),
    scrollContent          = document.querySelector('.scroll-content'),
    tooltipTriggerList     = document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    tooltipList            = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl)),
    productItems           = document.querySelector('.product-items'),
    relatedProducts        = document.querySelector('.swiperRelated'),
    cartLink               = document.getElementById('cartLink'),
    cartDeleteItemBtns     = document.querySelectorAll('.cart .cart-item .item-actions .delete-item'),
    confirmPhoneButton     = document.getElementById('getConfirmPhone'),
    actionAddressButtons   = document.querySelectorAll('[data-js-action=getFromDeliveryAddress], [data-js-action=editDeliveryAddress]'),
    otherAddress           = document.getElementById('otherAddress'),
    inputPhones            = document.querySelectorAll('#customerPhone, #emailLogin, #emailRegistration, #email'),
    slidersFilter          = document.querySelectorAll('.slider-styled'),
    collapseFilterItemBnt  = document.querySelectorAll('.filter-item .btn-link'),
    dropButtons            = document.querySelectorAll('[data-bs-toggle=dropdown]'),
    orderForm              = document.getElementById('orderForm'),
    filterBlock            = document.querySelector('aside[data-js-filter]');

if (modal) {
    modal.addEventListener('hide.bs.modal', function() {
        this.querySelector('.modal-title').textContent = '';
        this.querySelector('.modal-body').innerHTML = '';
    });
}

function removeHash () {
    let scrollV, scrollH, loc = window.location;
    if ("pushState" in history)
        history.pushState("", document.title, loc.pathname + loc.search);
    else {
        scrollV = document.body.scrollTop;
        scrollH = document.body.scrollLeft;

        loc.hash = "";

        document.body.scrollTop = scrollV;
        document.body.scrollLeft = scrollH;
    }
}

if (inputPhones.length > 0) {
    inputPhones.forEach(function (inputPhone) {
        inputPhone.addEventListener('keydown', function (event) {
            const isNumber = isFinite(event.key);
            if (isNumber && event.target.value.length >= 16) {
                event.preventDefault();
                event.stopPropagation();
            }
        });
        IMask(inputPhone,
            {
                mask: [
                    {
                        mask: '+{7}(000)000-00-00'
                    },
                    {
                        mask: /^\S*@?\S*$/
                    }
                ]
            });
    });
}

if(otherAddress) {
    const hiddenBlocks = otherAddress.closest('.delivery-address-block').querySelectorAll('.d-none');
    if (hiddenBlocks.length > 0) {
        otherAddress.addEventListener('click', function (event) {
            event.preventDefault();
            const defaultText = 'Указать другой адрес достааки',
                changeText    = 'Скрыть форму адреса доставки'

            hiddenBlocks.forEach(function (hiddenBlock) {
                hiddenBlock.classList.toggle('d-none');
            });

            if (hiddenBlocks[0].classList.contains('d-none')) {
                this.textContent = defaultText;
            } else {
                this.textContent = changeText;
            }
        });
    }
}

if (window.location.hash) {
    const tab = document.getElementById(window.location.hash.replace('#', ''));
    if (tab) {
        removeHash();
        const tabs = tab.closest('.nav.nav-pills'),
            allTabs = tabs.querySelectorAll('li button');

        allTabs.forEach(trigger => {
            const tabTrigger = new Tab(trigger);

            trigger.addEventListener('click', event => {
                event.preventDefault();
                tabTrigger.show();
            })
        });
        window.Tab.getInstance(tab).show();
    }
}

if (actionAddressButtons.length > 0) {
    actionAddressButtons.forEach(function (addButton) {
        addButton.addEventListener('click', function (event) {
            event.preventDefault();
            let url = this.dataset.addressId ?
                '/cabinet/profile/add-delivery-address?address_id='+this.dataset.addressId :
                '/cabinet/profile/add-delivery-address';

            axios.get(url).then(function (response) {
                if (response.status === 200 && response.statusText === 'OK') {
                    const html   = new DOMParser().parseFromString(response.data, 'text/html'),
                        body     = html.querySelector('body'),
                        headText = body.querySelector('h5').textContent;

                    body.querySelector('h5').remove();
                    modal.querySelector('.modal-body').innerHTML    = body.innerHTML;
                    modal.querySelector('.modal-title').textContent = headText;
                    modalWindow.show();
                }
            }).catch(function (error) {
                console.error(error);
            })
        });
    });
}

if (tooltipList.length > 0) {
    tooltipList.forEach(function (tooltip) {
        tooltip._element.addEventListener('click', function () {
            tooltip.hide();
        });
    })
}

if (buttonGetProfileForm) {
    function sendProfile() {
        let form = modal.querySelector('form'),
            formData = new FormData(form);

        axios.post(form.action, formData).then(function (response) {
            if (response.data.action === 'need-confirm') {
                getForm(null,'Подтверждение номера телефона');
            }
            if (response.data.action === 'confirm-done') {
                modal.querySelector('.modal-body').innerHTML = "<p>" + response.data.success + "</p>"
                if (modal.querySelector('.btn.btn-blue-dark')) {
                    modal.querySelector('.btn.btn-blue-dark').remove();
                }
                let closeTimeout = setTimeout(function () {
                    modalWindow.hide();
                    clearTimeout(closeTimeout);
                    window.location.reload();
                }, 3000);
            }

        }).catch(function (error) {
            console.error(error);
        });
    }

    function getForm (event, title = 'Редактировать данные профиля') {

        axios.get('/cabinet/profile/edit').then(function (data) {

            modal.querySelector('.modal-body').innerHTML = (data.data);
            modal.querySelector('.modal-title').textContent = title;

            if (!modal.querySelector('.btn.btn-blue-dark')) {
                let submitButton = document.createElement('button');
                submitButton.setAttribute('type', 'button')
                submitButton.textContent = 'Сохранить';
                submitButton.setAttribute('class', 'btn btn-blue-dark');
                modal.querySelector('.modal-footer').append(submitButton);
            }

            if (!modal.classList.contains('show')) {
                modalWindow.show();
            }

            modal.querySelector('.btn.btn-blue-dark').addEventListener('click', sendProfile);

            modal.addEventListener('hidden.bs.modal', function () {
                if (modal.querySelector('.btn.btn-blue-dark')) {
                    modal.querySelector('.btn.btn-blue-dark').remove();
                }
                modal.querySelector('.modal-title').textContent = '';
                modal.querySelector('.modal-body').innerHTML = '';
            });
        }).catch(function (error) {
            console.error(error);
        });
    }
    buttonGetProfileForm.addEventListener('click', getForm);
}

if (confirmPhoneButton) {
    confirmPhoneButton.addEventListener('click', function (event) {
        event.preventDefault();
        axios.post('/verify-exists-phone').then(function (response) {
            if (response.status === 200 && response.statusText === 'OK') {
                const html  = new DOMParser().parseFromString(response.data, 'text/html');
                modal.querySelector('.modal-body').innerHTML = html.querySelector('body').innerHTML;
                modalWindow.show();
                modal.querySelector('button[type=submit]').addEventListener('click', function (event) {
                    event.preventDefault();
                    event.target.closest('form').submit();
                    modalWindow.hide();
                    modal.querySelector('.modal-body').innerHTML = '';
                });
            } else {
                console.error(response.data);
            }

        }).catch(function (error) {
            console.error(error);
        });
    });
}

if (carousel) {
    new Swiper('.carousel', {
        loop:true,
        slidesPerView: 2,
        spaceBetween: 15,
        centeredSlides: true,
        height: 380,
        autoplay: {
            delay: 5000,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        modules: [Navigation, Autoplay],
        breakpoints: {
            1900: {
                slidesPerView:4
            },
            1500: {
                slidesPerView:3
            },
            992: {
                slidesPerView:2
            },
            768: {
                slidesPerView: 4,
                spaceBetween: 34
            }
        }
    })
}

if (document.querySelector('.reviews-swiper')) {
    new Swiper('.reviews-swiper', {
        loop:true,
        slidesPerView:1,
        effect: 'creative',
        autoplay: {
            delay: 5000,
        },
        navigation: {
            nextEl: '.review-button-next',
            prevEl: '.review-button-prev',
        },
        pagination: {el:'.swiper-pagination',clickable: true},
        modules: [Navigation, Autoplay, Pagination]
    });
}

if (megaMenu) {
    const image    = megaMenu.querySelector('.menu-image img'),
        links      = megaMenu.querySelectorAll('a'),
        buttons    = megaMenu.querySelectorAll('button'),
        swElements = megaMenu.querySelectorAll('.swiperMegaMenu');

    if (links.length > 0) {
        links.forEach(function (link) {
            const imageSrc = link.dataset.image;
            if (imageSrc) {
                link.addEventListener('mouseover', function (event) {
                    image.src = imageSrc;
                });
            }
        });
    }

    if(window.matchMedia("(max-width: 768px)").matches) {
        const tabs       = megaMenu.querySelectorAll('.tab-pane'),
            submenuLinks = megaMenu.querySelectorAll('.head-submenu > a');

        buttons.forEach(function(button) {
            if (button.classList.contains('active')) {
                button.classList.remove('active');
            }
            button.addEventListener('click',function (event) {
                event.preventDefault();
                const cloneButton = this.cloneNode(true),
                    targetId      = cloneButton.dataset.bsTarget.replace('#', ''),
                    tmpItem       = document.createElement('div'),
                    container     = document.getElementById(targetId),
                    self          = this;

                delete(cloneButton.dataset.bsToggle);
                delete(cloneButton.dataset.bsTarget);

                cloneButton.removeAttribute('aria-controls');
                cloneButton.removeAttribute('aria-selected');
                cloneButton.classList.remove('active');

                tmpItem.classList.add('head-submenu', 'back');
                tmpItem.append(cloneButton);

                container.prepend(tmpItem);

                cloneButton.addEventListener('click', function (e) {
                    container.classList.remove('active', 'show');
                    this.closest('.head-submenu.back').remove();
                    self.classList.remove('active');
                    self.setAttribute('aria-selected', "false");
                });
           });
        });

        tabs.forEach(function (tab) {
            if (tab.classList.contains('show')) {
                tab.classList.remove('show');
                tab.classList.remove('active');
            }
        });

        links.forEach(function (link) {
            if (link.classList.contains('active')) {
                link.classList.remove('active');
            }
        });

        submenuLinks.forEach(function (subLink) {
            subLink.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                submenuLinks.forEach(function (sub) {
                    if (sub.getAttribute('href') !== event.target.getAttribute('href')) {
                        sub.classList.remove('active');
                        sub.nextElementSibling.classList.remove('show');
                    }
                });
                if (this.classList.contains('active')) {
                    this.classList.remove('active');
                    this.nextElementSibling.classList.remove('show');
                } else {
                    this.classList.add('active');
                    this.nextElementSibling.classList.add('show');
                }
            });
        });
    } else if (swElements.length > 0) {
        swElements.forEach(function (sw) {
            new Swiper(sw, {
                direction: "vertical",
                slidesPerView: "auto",
                freeMode: true,
                scrollbar: {
                    el: sw.querySelector('.swiper-scrollbar'),
                },
                mousewheel: true,
                modules: [Scrollbar, Mousewheel, FreeMode]
            });
        });
    }


}

class MainGallery extends HTMLElement
{
    constructor() {
        super();
        this.fullSwiper    = this.querySelector('.full-swiper');
        this.thumbSwiper   = this.querySelector('.thumbs-swiper');
        this.fullScreenBtn = this.querySelector('.full-screen-button');

        this.fullScreenBtn.addEventListener('click', this.fullScreenToggle.bind(this));

        this.initVideoPlayer();
        this.initSwiper(this.fullSwiper, this.thumbSwiper);
    }

    fullScreenToggle(event) {
        event.preventDefault();

        const images = this.querySelectorAll('.full-swiper img');

        if (!document.fullscreenElement) {
            images.forEach(function (image) {
                const src = image.src;
                image.src = src.replace('large_', 'full_');
            });
            this.classList.add('fullscreen');
            this.requestFullscreen().then(function () {
                event.target.querySelector('.close').classList.remove('d-none');
                event.target.querySelector('.open').classList.add('d-none');
            });
        } else {
            images.forEach(function (image) {
                const src = image.src;
                image.src = src.replace('full_', 'large_');
            });
            this.classList.remove('fullscreen');
            document.exitFullscreen().then(function () {
                event.target.querySelector('.close').classList.add('d-none');
                event.target.querySelector('.open').classList.remove('d-none');
            });
        }

    }

    initVideoPlayer() {
        const videos = this.querySelectorAll('video');
        if (videos.length > 0) {
            videos.forEach(function (video) {
                videojs(video, {
                    controls:true
                });
            });
        }
    }

    initSwiper(fullSwiper, thumbSwiper) {
        let swiper = null;
        if (thumbSwiper) {
            swiper = new Swiper(thumbSwiper, {
                spaceBetween: 10,
                slidesPerView: "auto",
                freeMode: true,
                watchSlidesProgress: true,
                modules: [Navigation, Autoplay, Pagination, Thumbs]
            });
        }
        if (fullSwiper && swiper) {
            new Swiper(fullSwiper, {
                slidesPerView: "auto",
                spaceBetween:10,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev"
                },
                thumbs: {
                    swiper: swiper,
                },
                modules: [Navigation, Autoplay, Pagination, Thumbs]
            });
        } else if (fullSwiper) {
            new Swiper(fullSwiper, {
                spaceBetween:10
            });
        }
    }
}

customElements.define('main-gallery', MainGallery);

if (bannerItems.length > 0) {
    setTimeout(function () {
        bannerItems.forEach(function (item) {
            item.classList.remove('fadein');
        });
    }, 1700);
}

if (scrollContent) {
    new Swiper(scrollContent, {
        direction: "vertical",
        slidesPerView: "auto",
        freeMode: true,
        scrollbar: {
            el: ".swiper-scrollbar",
        },
        mousewheel: true,
        modules: [Scrollbar, Mousewheel, FreeMode]
    });
}
class ProductQuantity extends HTMLElement {
    constructor() {
        super();
        const buttons = this.querySelectorAll('button');
        const input   = this.querySelector('input');

        if (!input) return ;

        const form = input.getAttribute('form') ?
            document.getElementById(input.getAttribute('form')) :
            input.closest('form');

        if (!form) return;

        const hiddenQuantity = form.querySelector('input[name=product_quantity]');

        this.getHiddenQty = function () {return hiddenQuantity}
        this.getInput     = function () {return input}
        this.getForm      = function () {return form}

        if (buttons.length > 0) {
            const self = this;
            buttons.forEach(function (button) {
                button.addEventListener('click', self.initClick.bind(self));
            });
        }

        if (input) {
            input.addEventListener('click', function(event) {
                event.preventDefault();
            });

            input.addEventListener('keyup', this.changeInput.bind(this));
        }
    }

    initClick(event) {
        event.preventDefault();
        event.stopPropagation();

        let value = Number.parseInt(this.getInput().value);

        if (event.target.classList.contains('plus')) {
            value = value+1;
        } else if (value > 0) {
            value = value-1;
        } else {
            value = 0;
        }

        if (this.closest('side-cart') || this.closest('.cart')) {
            const formData = new FormData(),
                self       = this;

            formData.append('quantity', value.toString());
            formData.append('item_id', this.dataset.itemId);

            axios.post(this.dataset.changeQuantity, formData).then(function (response) {
                if (response.status === 200 && response.statusText === 'OK') {
                    const html   = new DOMParser().parseFromString(response.data, 'text/html'),
                        sideCart = document.querySelector('side-cart'),
                        cart     = document.querySelector('.cart');

                    if (cart) {
                        window.location.reload();
                    }

                    sideCart.innerHTML = '';
                    sideCart.append(html.querySelector('.side-cart__wrapper'));
                    sideCart.initCloseButton(sideCart.querySelector('.side-cart__header span'));
                    sideCart.initDeleteItem();

                } else {
                    console.error(response.data.message);
                }

            }).catch(function (error) {
                console.log(error);
            });

        } else {
            this.getHiddenQty().value = value.toString();
            this.getInput().value     = value.toString();

            const eventChange = new Event('keyup');
            this.getInput().dispatchEvent(eventChange);
        }
    }

    changeInput(event) {
        const maxValue = event.target.dataset.orderType === 'checkout' ? event.target.dataset.maxQuantity : 999;
        if (Number.parseInt(event.target.value) > Number.parseInt(maxValue) || Number.parseInt(event.target.dataset.maxQuantity) <= 0) {
            this.getForm().querySelector("button[type=submit]").textContent = 'Создать заказ';
            this.getForm().querySelector("input[name=type_order]").value    = 'order';
        } else {
            this.getForm().querySelector("button[type=submit]").textContent = 'В корзину';
            this.getForm().querySelector("input[name=type_order]").value    = 'checkout';
        }
        this.getHiddenQty().value = event.target.value;
    }
}

customElements.define('product-quantity', ProductQuantity);

if (productItems) {
    const options = {
        rootMargin: '0px',
        threshold: 1
    },
        target  = document.getElementById('getMoreProducts'),
        spinner = document.getElementById('productsLoader');

    let previousY      = 0,
        previousRatio  = 0;

    document.addEventListener('DOMContentLoaded', function () {
        let newUrl = new URL(window.location.origin+window.location.pathname+location.search);
        newUrl.searchParams.delete('page');
        window.history.pushState({path:newUrl.href}, '', newUrl)
    });

    const lazyLoadProducts = function () {
        spinner.classList.remove('invisible');
        let link   = new URL(window.location),
            page   = link.searchParams.get('page') ?? 1,
            newUrl = !target.dataset.url ? window.location.origin+window.location.pathname+'?page='+(Number.parseInt(page)+1): target.dataset.url+'&page='+(Number.parseInt(page)+1);

        window.history.pushState({path:newUrl}, '', newUrl);

        axios.get(newUrl).then(response => {
            const doc    = new DOMParser().parseFromString(response.data, 'text/html'),
                newItems = doc.querySelectorAll('.product-item');
            if (newItems.length > 0) {
                newItems.forEach(newItem => {
                    productItems.append(newItem);
                });
                spinner.classList.add('invisible');
            } else {
                newUrl = new URL(window.location.origin+window.location.pathname+location.search);
                newUrl.searchParams.delete('page');
                window.history.pushState({path:newUrl.href}, '', newUrl.href);
                target.remove();
                if (!spinner.classList.contains('invisible')) {
                    spinner.classList.add('invisible');
                }
                if (document.querySelector('footer').classList.contains('d-none')) {
                    document.querySelector('footer').classList.remove('d-none');
                }
            }
        }).catch(error => {
            console.error(error);
            document.querySelector('footer').classList.remove('d-none');
            document.querySelector('aside').classList.add('bottom');
            newUrl = new URL(window.location.origin+window.location.pathname+location.search);
            newUrl.searchParams.delete('page');
            window.history.pushState({path:newUrl.href}, '', newUrl.href);
            target.remove();
            if (!spinner.classList.contains('invisible')) {
                spinner.classList.add('invisible');
            }
        });
    }

    const callback = function (entries) {
        entries.forEach(entry => {
            const currentY = entry.boundingClientRect.y,
                currentRatio = entry.intersectionRatio,
                isIntersecting = entry.isIntersecting;

            if (currentY < previousY) {
                if (currentRatio > previousRatio && isIntersecting) {
                    document.querySelector('footer').classList.add('d-none');
                    lazyLoadProducts();
                }
            }
            previousY = currentY;
            previousRatio = currentRatio;
        });
    }

    const observer = new IntersectionObserver(callback, options);

    observer.observe(target);
}

if (relatedProducts) {
    new Swiper(relatedProducts, {
        effect: "coverflow",
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: "auto",
        initialSlide: 1,
        loop: true,
        coverflowEffect: {
            rotate: 50,
            stretch: 0,
            depth: 100,
            modifier: 1,
            slideShadows: true
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true
        },
        modules: [Mousewheel, FreeMode, EffectCoverflow, Pagination]
    });
}

class ProductForm extends HTMLElement
{
    constructor() {
        super();
        this.form = this.querySelector('form');
        if (!this.form) return;
        this.buyButton = this.form.querySelector('button[type=submit]');
        this.buyButton.addEventListener('click', this.clickBuy.bind(this));
    }

    clickBuy(event) {
        event.preventDefault();
        const formData = new FormData(this.form);

        axios.post(this.form.action, formData).then(function (response) {
            if (response.status === 200 && response.statusText === 'OK') {
                const html    = new DOMParser().parseFromString(response.data, 'text/html'),
                    sideCart  = document.querySelector('side-cart'),
                    alertEl   = document.querySelector('.alert-danger'),
                    alertElR  = html.querySelector('.alert-danger'),
                    destCount = document.querySelector('#cartLink .badge');
                destCount.textContent = html.querySelector('.side-cart').dataset.countItems;

                if (alertElR) {
                    const breadcrumbs = document.querySelector('.breadcrumbs');
                    breadcrumbs.after(alertElR);
                    window.scrollTo(0, 0);
                } else {
                    if (alertEl) { alertEl.remove(); }
                    sideCart.innerHTML = '';
                    sideCart.append(html.querySelector('.side-cart__wrapper'));
                    sideCart.initCloseButton(sideCart.querySelector('.side-cart__header span'));
                    sideCart.initSwiper();
                    sideCart.initDeleteItem();

                    document.body.classList.add('open-side-cart');
                    sideCart.classList.add('open');
                }
            } else {
                console.error(response.data.message);
            }
        }).catch(function (error) {
            console.error(error);
        });
    }
}

customElements.define('product-form', ProductForm);

class SideCart extends HTMLElement
{
    constructor() {
        super();
        this.closeButton = this.querySelector('.material-symbols-outlined.close');

        if (!this.closeButton) return;
        this.initCloseButton(this.closeButton);
        this.addEventListener('click', this.closeOnOverlay.bind(this));

        this.initSwiper();
        this.initDeleteItem();
    }

    open() {
        this.classList.add('open');
        document.body.classList.add('open-side-cart');

    }

    closeOnOverlay(event) {
        if (event.target.classList.contains('side-cart')) {
            document.body.classList.remove('open-side-cart');
            this.classList.remove('open');
        }
    }

    initSwiper() {
        const swiperCartItems = document.querySelector('.side-cart-scroll-content');
        new Swiper(swiperCartItems, {
            direction: "vertical",
            slidesPerView: "auto",
            freeMode: true,
            scrollbar: {
                el: ".side-cart-scrollbar",
            },
            mousewheel: true,
            modules: [Scrollbar, Mousewheel, FreeMode]
        });
    }

    initCloseButton(button) {
        button.addEventListener("click", function (event) {
            event.preventDefault();
            document.body.classList.remove('open-side-cart');
            button.closest('.side-cart').classList.remove('open');
        });
    }

    initDeleteItem() {

        const items   = this.querySelectorAll('.cart-item'),
            formatter = new Intl.NumberFormat('ru-RU', {style:'currency', currency: 'RUB', minimumFractionDigits: 0}),
            self      = this;

        if (items.length > 0) {
            items.forEach(function (item) {

                const deleteButton = item.querySelector('.item-actions span.material-symbols-outlined'),
                    Id             = item.querySelector('product-quantity').dataset.itemId,
                    formData       = new FormData();

                formData.append('item_id', Id);


                deleteButton.addEventListener('click', function (event) {
                    event.preventDefault();
                    axios.post('/cart/delete-item', formData).then(function (response) {
                        if (response.data.hasOwnProperty('status') && response.data.status === 'success') {
                            const cartBadge = document.getElementById('cartLink').querySelector('span'),
                                headerHref  = document.querySelector('.side-cart__header > a'),
                                totalAmount = document.querySelector('.side-cart__footer .total-amount span'),
                                currentSum  = Number.parseInt(totalAmount.textContent.trim().replace(' ', '').replace(/\u00a0/g, "")),
                                priceItem   = item.querySelector('.item-actions .subtotal-price'),
                                sumItem     = Number.parseInt(priceItem.textContent.trim().replace(' ', '').replace(/\u00a0/g, ""));

                            let currentQuantity = Number.parseInt(cartBadge.textContent) - 1,
                                currentTotal    = currentSum - sumItem;

                            cartBadge.textContent = currentQuantity.toString();
                            headerHref.textContent = "Ваши покупки (" + currentQuantity.toString() + ")";

                            totalAmount.textContent = formatter.format(currentTotal);

                            if (currentTotal === 0) {
                                headerHref.remove();
                                const header     = document.querySelector('.side-cart__header');
                                header.innerHTML = 'Ваша корзина пуста' + '<span class="material-symbols-outlined close">close</span>';
                                document.querySelector('.side-cart__footer').innerHTML = '';
                                document.querySelector('.side-cart__content').innerHTML = '';
                                self.initCloseButton(self.querySelector('.material-symbols-outlined.close'));
                            } else {
                                item.remove();
                            }
                        }
                    }).catch(function (error) {
                        console.error(error);
                    });

                });
            });
        }
    }
}

customElements.define('side-cart', SideCart);

if (cartLink) {
    const cartSide = document.querySelector('.side-cart');

    cartLink.addEventListener('click', function (event) {
        event.preventDefault();
        cartSide.open();
    });
}

if (cartDeleteItemBtns.length > 0) {
    cartDeleteItemBtns.forEach(function (deleteButton) {
        deleteButton.addEventListener('click', function (event) {
            event.preventDefault();
            const item   = event.target.closest('.cart-item'),
                itemId   = item.querySelector('product-quantity').dataset.itemId,
                formData = new FormData();

            formData.append('item_id', itemId);

            axios.post('/cart/delete-item', formData).then(function (response) {
                if (response.data.hasOwnProperty('status') && response.data.status === 'success') {
                    window.location.reload();
                }
            }).catch(function (error) {
                console.error(error);
            })
        });
    });
}

if (slidersFilter.length > 0) {

    const filterForm = document.querySelector('.form-filter'),
        formatter    = new Intl.NumberFormat('ru-RU', {style:'currency', currency: 'RUB', minimumFractionDigits: 0});

    slidersFilter.forEach(function (slider) {
        const valuesForSlider = JSON.parse(slider.dataset.steps),
            inputMin          = slider.closest('.filter-item').querySelector("[js-name=minValue]"),
            inputMax          = slider.closest('.filter-item').querySelector("[js-name=maxValue]"),
            minDisplayPrice   = slider.closest('.filter-item').querySelector("[js-name=minValueDisplayPrice]"),
            maxDisplayPrice   = slider.closest('.filter-item').querySelector("[js-name=maxValueDisplayPrice]"),
            minDisplay        = slider.closest('.filter-item').querySelector("[js-name=minValueDisplay]"),
            maxDisplay        = slider.closest('.filter-item').querySelector("[js-name=maxValueDisplay]");

        const format = {
            to: function(value) {
                return valuesForSlider[Math.round(value)];
            },
            from: function (value) {
                return valuesForSlider.indexOf(value);
            }
        };

        noUiSlider.create(slider, {
            start: [valuesForSlider[0], valuesForSlider[valuesForSlider.length - 1]],
            range: { min: 0, max: valuesForSlider.length - 1 },
            step: 1,
            tooltips: false,
            format: format,
            connect: true
        });

        if (slider.dataset.factMin && slider.dataset.factMax) {
            slider.noUiSlider.set([slider.dataset.factMin, slider.dataset.factMax]);
        }

        slider.noUiSlider.on('update', function (values, handle) {

            if (maxDisplayPrice)
                maxDisplayPrice.textContent = formatter.format(Number.parseInt(values[1]));

            if (minDisplayPrice)
                minDisplayPrice.textContent = formatter.format(Number.parseInt(values[0]));

            if (minDisplay)
                minDisplay.textContent = values[0];

            if (maxDisplay)
                maxDisplay.textContent = values[1];

            inputMax.value = values[1];
            inputMin.value = values[0];
        });

        slider.noUiSlider.on('end', function (values, handle) {
            filterForm.submit();
        });
    });

    const inputs = filterForm.querySelectorAll('input');

    if (inputs.length > 0) {
        inputs.forEach(function (itemInput) {
            itemInput.addEventListener('change', function (event) {
                filterForm.submit();
            });
        });
    }
}

if(collapseFilterItemBnt.length > 0) {
    collapseFilterItemBnt.forEach(function (button) {
        const container = button.closest('.filter-item'),
            defaultText = button.textContent,
            altText     = 'Скрыть';

        button.addEventListener('click', function (event) {
            container.classList.toggle('collapsed');
            if (container.classList.contains('collapsed')) {
                button.textContent = defaultText;
            } else {
                button.textContent = altText;
            }
        });
    });

    if(window.matchMedia("(max-width: 992px)").matches) {
        const showFilterButton = document.getElementById('showFilter'),
            closeFilter        = document.getElementById('closeFilter'),
            filter             = document.querySelector('.row.products .col-lg-3');

        let scrollTop = 0;

        if (showFilterButton) {
            showFilterButton.addEventListener('click', function (event) {
                event.preventDefault();

                filter.classList.toggle('show');
                console.log(window.scrollY);
                if (filter.classList.contains('show')) {
                    window.scrollTo(0,scrollTop);
                    scrollTop = window.scrollY;
                }
            });
        }
        if (closeFilter) {
            closeFilter.addEventListener('click', function (event) {
                event.preventDefault();
                filter.classList.remove('show');
                window.scrollTo(0, scrollTop);
                scrollTop = 0;
            })
        }
    }
}

if (dropButtons.length > 0) {
    dropButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            const currentDropId = button.dataset.bsTarget.replace('#', ''),
                dropdowns = document.querySelectorAll('.dropdown-menu');

            dropdowns.forEach(function (dropElement) {
                if (dropElement.id !== currentDropId) {
                    const btnInstance = Dropdown.getInstance(document.querySelector('[data-bs-target="#' + dropElement.id + '"]'));

                    if (btnInstance) {
                        btnInstance.hide();
                    }
                }
            });
        });
    });
}

class SearchForm extends HTMLElement
{
    constructor() {
        super();
        const inputQuery = this.querySelector('input#search');
        const allResult  = this.querySelector('.search-icon');
        this.checkAnswer = false;

        inputQuery.addEventListener('input', this.initInput.bind(this));
        inputQuery.addEventListener('focus', this.initFocus.bind(this));
        document.body.addEventListener('click', this.initClose.bind(this));
        allResult.addEventListener('click', this.showAllResult.bind(this));
    }

    initFocus = function (event) {
        event.preventDefault();
        if (this.querySelector('input#search').classList.contains('has-error')) {
            this.querySelector('input#search').classList.remove('has-error');
        }
        const megaMenuButton = document.querySelector('.btn.btn-catalog');
        if (megaMenuButton.classList.contains('show')) {
            const dropdownInstance = Dropdown.getInstance(megaMenuButton);
            dropdownInstance.hide();
        }
    }

    initClose = function (event)
    {
        const result = this.querySelector('.search-result-container');
        if (!event.target.closest('search-form') && result) {
            this.querySelector('form').reset();
            result.remove();
            this.checkAnswer = false;
        }
    }

    showAllResult = function (event)
    {
        event.preventDefault();
        const input = this.querySelector('input#search');

        if (input.value === '') {
            input.classList.add('has-error');
        } else {
            window.location.href = '/shop/search?query='+input.value;
        }
    }

    initInput = function (event) {
        event.preventDefault();

        const self = this,
            result = self.querySelector('.search-result-container');

        if (event.target.value.length > 2) {
            const formData = new FormData();

            formData.append('query', event.target.value);

            axios.post('/shop/ajax-search', formData).then(function (response) {
                const answer = new DOMParser().parseFromString(response.data, 'text/html');

                if (!result && !self.checkAnswer) {
                    self.append(answer.querySelector('.search-result-container'));
                } else if (result && self.checkAnswer) {
                    result.innerHTML = answer.querySelector('.search-result-container').innerHTML;
                }

                new Swiper('.mySwiperSearchContent', {
                    direction: "vertical",
                    slidesPerView: "auto",
                    freeMode: true,
                    scrollbar: {
                        el: ".swiper-scrollbar",
                    },
                    mousewheel: true,
                    modules: [Scrollbar, Mousewheel, FreeMode]
                });

                self.checkAnswer = true;

                const showAllResult = self.querySelector('.show-all button');

                if (showAllResult) {
                    showAllResult.addEventListener('click', self.showAllResult.bind(self));
                }

            }).catch(function (error) {
                console.error(error);
            })
        }

    }
}

customElements.define('search-form', SearchForm);

if (orderForm) {
    const deliveryMethods = document.getElementById('deliveryMethods'),
        addressBlock      = document.querySelector('.delivery-address-block'),
        inputs            = addressBlock.querySelectorAll('input');

    if (deliveryMethods) {

        if (deliveryMethods.options[deliveryMethods.options.selectedIndex].text === 'Самовывоз') {
            inputs.forEach(function (input) {
                input.removeAttribute('required');
            });
            addressBlock.classList.add('d-none');
        }

        deliveryMethods.addEventListener('change', function (event) {
            event.preventDefault();
            if (event.target.options[event.target.selectedIndex].text === 'Самовывоз') {
                inputs.forEach(function (input) {
                    input.removeAttribute('required');
                });
                addressBlock.classList.add('d-none');
            } else {
                inputs.forEach(function (input) {
                    input.setAttribute('required', 'required');
                });
                addressBlock.classList.remove('d-none');
            }
        });
    }
}

if (filterBlock && window.matchMedia("(min-width: 992px)").matches) {
    const beginFilter = document.getElementById('beginFilter'),
        footer = document.querySelector('footer');

    let lastScrollTop = 0;

    document.addEventListener("scroll", function() {
        let st = window.scrollY || document.documentElement.scrollTop;
        if (st > lastScrollTop) {

            if (footer.getBoundingClientRect().y < window.innerHeight && footer.getBoundingClientRect().y !== 0) {
                filterBlock.classList.add('bottom');
            }

            if (beginFilter.getBoundingClientRect().y <= 0) {
                filterBlock.classList.add('sticky');
            }

        } else if (st < lastScrollTop) {
            filterBlock.classList.remove('bottom');
            if (beginFilter.getBoundingClientRect().y >= 0) {
                filterBlock.classList.remove('sticky');
            }
        }
        lastScrollTop = st <= 0 ? 0 : st;
    }, false);
}
