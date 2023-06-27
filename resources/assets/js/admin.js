import './bootstrap';
import Choices from "choices.js";
import Swal from "sweetalert2";
import feather from 'feather-icons/dist/feather.min';
import button from "bootstrap/js/src/button";
import Sortable from "sortablejs";

const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    tooltipList          = [...tooltipTriggerList].map(tooltipTriggerEl => new Tooltip(tooltipTriggerEl)),
    forms                = document.querySelectorAll('form'),
    choices              = document.querySelectorAll('.js-choices'),
    mainCategorySelect   = document.querySelector('#productForm [name=category_id]'),
    attributesBlock      = document.getElementById('newAttributes'),
    variantsBlock        = document.getElementById('newVariants'),
    productTable         = document.getElementById('productTable'),
    deliveryTable        = document.getElementById('deliveryTable'),
    usersTable           = document.getElementById('userTable'),
    orderTable           = document.getElementById('orderTable'),
    createMenuButton     = document.getElementById('jsCreateMenu'),
    editMenuButton       = document.querySelectorAll('.edit-menu'),
    addNavItems          = document.querySelectorAll('.add-items'),
    addGroupItems        = document.getElementById('addFilterGroup');

if (tooltipList.length > 0) {
    tooltipList.forEach(function (tooltip) {
        tooltip._element.addEventListener('click', function () {
            tooltip.hide();
        });
    })
}

(() => {
    'use strict'
    feather.replace({ 'aria-hidden': 'true' });
    const ctx = document.getElementById('myChart');
    if (ctx) {const myChart = new Chart(ctx, {type: 'line',data: {labels: ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],datasets: [{data: [15339,21345,18483,24003,23489,24092,12034],lineTension: 0,backgroundColor: 'transparent',borderColor: '#007bff',borderWidth: 4,pointBackgroundColor: '#007bff'}]},options: {scales: {yAxes: [{ticks: {beginAtZero: false}}]},legend: {display: false}}});}
})();


if (addGroupItems) {
    let newGroupCount = 0;
    addGroupItems.addEventListener('click', function (event) {
        axios.post('/admin/shop/filters/add-group').then(function (response) {
            if (response.status === 200 && response.statusText === 'OK') {
                const html = new DOMParser().parseFromString(response.data, 'text/html');
                newGroupCount++;
                html.querySelector('.accordion-header').id = 'heading-new-'+newGroupCount;
                const collapseBlock = html.querySelector('.accordion-collapse'),
                    buttonCollapse  = html.querySelector('.accordion-header button'),
                    inputName       = html.querySelector("[name='group_name[]']"),
                    labelName       = inputName.nextElementSibling,
                    inputCategories = html.querySelector("[name='group_categories[][]']"),
                    inputTags       = html.querySelector("[name='tags[][]']"),
                    inputAttributes = html.querySelector("[name='attributes[][]']"),
                    inputCheckbox   = html.querySelector("[name='display_head[]']"),
                    labelCheckbox   = inputCheckbox.nextElementSibling,
                    container       = document.getElementById('accordionGroups');

                inputName.setAttribute('name', 'group_name[new-'+newGroupCount+']');
                inputName.id = 'groupName-'+newGroupCount;
                labelName.setAttribute('for', 'groupName-'+newGroupCount);

                inputCategories.setAttribute('name', 'group_categories[new-'+newGroupCount+'][]');
                inputAttributes.setAttribute('name', 'attributes[new-'+newGroupCount+'][]');
                inputTags.setAttribute('name', 'tags[new-'+newGroupCount+'][]');

                inputCheckbox.setAttribute('name', 'display_head[new-'+newGroupCount+']');
                inputCheckbox.id = 'groupDisplayHead-'+newGroupCount;
                labelCheckbox.setAttribute('for', 'groupDisplayHead-'+newGroupCount);

                collapseBlock.classList.remove('show');
                collapseBlock.id = 'collapse-new-'+newGroupCount;
                collapseBlock.setAttribute('aria-labelledby', 'heading-new-'+newGroupCount);

                buttonCollapse.dataset.bsTarget = '#collapse-new-'+newGroupCount;
                buttonCollapse.setAttribute('aria-expanded', 'false');
                buttonCollapse.setAttribute('aria-controls', 'collapse-new-'+newGroupCount);

                const choices = html.querySelectorAll('.js-choices');
                if(choices.length > 0) {
                    initChoices(choices);
                }

                container.append(html.querySelector('.accordion-item'));
            }

        }).catch(function (error) {
            console.error(error);
        });
    });
}

if (productTable) {
    const searchForm = productTable.querySelector('#searchProducts'),
        selectAll    = productTable.querySelector('[name=select-all]'),
        allCheckboxes = productTable.querySelectorAll('[name="selected[]"]');

    selectAll.addEventListener('change', function (event) {
        allCheckboxes.forEach(function (checkbox) {
            checkbox.checked = event.target.checked;
        });
    });

    Array.from(searchForm.elements).forEach(function (element) {
        if (element.type === 'select-one' || element.type === 'checkbox') {
            element.addEventListener('change', function () {
                searchForm.submit();
            });
        }
        if (element.type === 'text') {
            element.addEventListener('keyup', function (event) {
                if (event.code === 'Enter' || event.code === 'NumpadEnter') {
                    searchForm.submit();
                }
            })
        }
    });
}

if (deliveryTable) {
    const searchForm = deliveryTable.querySelector('#searchDelivery'),
        selectAll    = deliveryTable.querySelector('[name=select-all]'),
        allCheckboxes = deliveryTable.querySelectorAll('[name="selected[]"]');

    selectAll.addEventListener('change', function (event) {
        allCheckboxes.forEach(function (checkbox) {
            checkbox.checked = event.target.checked;
        });
    });

    Array.from(searchForm.elements).forEach(function (element) {
        if (element.type === 'select-one' || element.type === 'checkbox') {
            element.addEventListener('change', function () {
                searchForm.submit();
            });
        }
        if (element.type === 'text') {
            element.addEventListener('keyup', function (event) {
                if (event.code === 'Enter' || event.code === 'NumpadEnter') {
                    searchForm.submit();
                }
            })
        }
    });
}

if (usersTable) {
    const searchForm  = usersTable.querySelector('#searchUsers'),
        selectAll     = usersTable.querySelector('[name=select-all]'),
        allCheckboxes = usersTable.querySelectorAll('[name="selected[]"]');

    selectAll.addEventListener('change', function (event) {
        allCheckboxes.forEach(function (checkbox) {
            checkbox.checked = event.target.checked;
        });
    });

    Array.from(searchForm.elements).forEach(function (element) {
        if (element.type === 'select-one' || element.type === 'checkbox') {
            element.addEventListener('change', function () {
                searchForm.submit();
            });
        }
        if (element.type === 'text') {
            element.addEventListener('keyup', function (event) {
                if (event.code === 'Enter' || event.code === 'NumpadEnter') {
                    searchForm.submit();
                }
            })
        }
    });
}

if (orderTable) {
    const searchForm  = orderTable.querySelector('#searchOrders'),
        selectAll     = orderTable.querySelector('[name=select-all]'),
        allCheckboxes = orderTable.querySelectorAll('[name="selected[]"]');

    selectAll.addEventListener('change', function (event) {
        allCheckboxes.forEach(function (checkbox) {
            checkbox.checked = event.target.checked;
        });
    });

    Array.from(searchForm.elements).forEach(function (element) {
        if (element.type === 'select-one' || element.type === 'checkbox') {
            element.addEventListener('change', function () {
                searchForm.submit();
            });
        }
        if (element.type === 'text') {
            element.addEventListener('keyup', function (event) {
                if (event.code === 'Enter' || event.code === 'NumpadEnter') {
                    searchForm.submit();
                }
            })
        }
    });
}

function submitForms(forms, refresh = false) {
    if(forms.length === 0) {
        console.error('Формы не найдены');
        return null;
    }
    forms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(form);
            axios.post(form.action, formData).then(function (response) {
                const answer = response.data,
                    answerBlock = document.createElement('div');
                answerBlock.classList.add('alert');
                if (typeof answer.error !== 'undefined') {
                    answerBlock.classList.add('bg-danger');
                    answerBlock.innerText = answer.error;
                } else if (typeof answer.success !== 'undefined') {
                    answerBlock.classList.add('bg-success');
                    answerBlock.innerText = answer.success;
                }
                answerBlock.classList.add('text-white');
                if (refresh) {
                    window.location.reload();
                }
                form.prepend(answerBlock);
            }).catch(function (error) {
                console.error(error);
            })
        });
    });
}

function toggleGallery(elements, forms, mainPhotos) {
    if (elements.length === 0) {
        console.error('Не найдены изображения');
        return null;
    }
    elements.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            const photoId = this.closest('.thumb-item').dataset.photoId;
            elements.forEach(function (element) {
                element.closest('.thumb-item').classList.remove('active');
            });
            forms.forEach(function (form) {
                if (typeof form.dataset.photoId !== 'undefined' && form.dataset.photoId === photoId) {
                    form.classList.add('active');
                } else {
                    form.classList.remove('active');
                }
            });
            mainPhotos.forEach(function (mainPhoto) {
                if (typeof mainPhoto.dataset.photoId !== 'undefined' && mainPhoto.dataset.photoId === photoId) {
                    mainPhoto.classList.add('active');
                } else {
                    mainPhoto.classList.remove('active');
                }
            });
            this.closest('.thumb-item').classList.add('active');
        });
    });
}

if (forms.length > 0) {
    const overlay = document.getElementById('mainOverlay');
    forms.forEach(function (form) {
       form.addEventListener('submit', function () {
           overlay.classList.add('show');
           document.body.classList.add('loading');
       });
    });
}

let sideBarMenu = document.querySelectorAll('.sidebar .nav-link'),
    jsConfirm = document.querySelectorAll('.js-confirm');

if (sideBarMenu.length > 0) {
    sideBarMenu.forEach(function (element) {
        element.addEventListener('click', function (event) {
            let nextEl = element.nextElementSibling,
                parentEl = element.parentElement;

            if (nextEl) {
                event.preventDefault();
                let myCollapse = new Collapse(nextEl);

                if (nextEl.classList.contains('show')) {
                    myCollapse.hide();
                } else {
                    myCollapse.show();

                    let openedSubmenu = parentEl.parentElement.querySelector('.submenu.show');

                    if (openedSubmenu) {
                        new Collapse(openedSubmenu);
                    }
                }
            }
        });
    });
}

function initChoices(elements) {
    elements.forEach(function (element) {
        let config = {
            loadingText: 'Загрузка...',
            noResultsText: 'Не найдено',
            noChoicesText: 'Нет выбора',
            itemSelectText: 'Выбрать',
            uniqueItemText: 'Можно добавлять только уникальные значения',
            customAddItemText: 'Можно добавлять только значения, соответствующие определенным условиям',
            placeholder:true,
            shouldSort: false,
            removeItems: true,
            removeItemButton: true,
            duplicateItemsAllowed: false,
            allowHTML: true,
            fuseOptions: {
                includeScore: true,
                threshold:0.5
            },
        }
        if (typeof element.dataset.customTemplate !== 'undefined') {
            config.callbackOnCreateTemplates = function (template) {
                return {
                    item: ({ classNames }, data) => {
                        if (data.value.indexOf('|') !== -1) {
                            const arrayValue = data.value.split('|'),
                                color = arrayValue[1];
                            if (typeof color !== 'undefined') {
                                return template(`
                                            <div class="${classNames.item} ${
                                    data.highlighted
                                        ? classNames.highlightedState
                                        : classNames.itemSelectable
                                } ${
                                    data.placeholder ? classNames.placeholder : ''
                                }" data-item data-id="${data.id}" data-value="${data.value}" ${
                                    data.active ? 'aria-selected="true"' : ''
                                } ${data.disabled ? 'aria-disabled="true"' : ''}>
                                    <i style="display: inline-block;width: 16px;height: 16px;background: ${color}"></i>${data.label}<button type="button" class="choices__button" data-button="">Remove item</button></div>
                                `);
                            }
                        } else {
                            return template(`
                                            <div class="${classNames.item} ${
                                data.highlighted
                                    ? classNames.highlightedState
                                    : classNames.itemSelectable
                            } ${
                                data.placeholder ? classNames.placeholder : ''
                            }" data-item data-id="${data.id}" data-value="${data.value}" ${
                                data.active ? 'aria-selected="true"' : ''
                            } ${data.disabled ? 'aria-disabled="true"' : ''}>${data.label}</div>`);
                        }
                    },
                    choice: ({ classNames }, data) => {
                        if (data.value.indexOf('|') !== -1) {
                            const arrayValue = data.value.split('|'),
                                color = arrayValue[1];
                            if (typeof color !== 'undefined') {
                                return template(`
                                            <div class="${classNames.item} ${classNames.itemChoice} ${
                                    data.disabled ? classNames.itemDisabled : classNames.itemSelectable
                                }" data-select-text="${this.config.itemSelectText}" data-choice ${
                                    data.disabled
                                        ? 'data-choice-disabled aria-disabled="true"'
                                        : 'data-choice-selectable'
                                } data-id="${data.id}" data-value="${data.value}" ${
                                    data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
                                }><i style="display: inline-block;width: 16px;height: 16px;background: ${color}"></i>${data.label}</div>`);
                            }
                        } else {
                            return template(`
                                            <div class="${classNames.item} ${classNames.itemChoice} ${
                                data.disabled ? classNames.itemDisabled : classNames.itemSelectable
                            }" data-select-text="${this.config.itemSelectText}" data-choice ${
                                data.disabled
                                    ? 'data-choice-disabled aria-disabled="true"'
                                    : 'data-choice-selectable'
                            } data-id="${data.id}" data-value="${data.value}" ${
                                data.groupId > 0 ? 'role="treeitem"' : 'role="option"'
                            }>${data.label}</div>`);
                        }
                    },
                }
            }
        }
        new Choices(element, config);

        element.addEventListener('removeItem', function (event) {
            if ('action' in event.target.dataset) {
                const url    = event.target.dataset['action'],
                    id       = event.detail.value,
                    formData = new FormData(),
                    overlay  = document.getElementById('mainOverlay');

                overlay.classList.add('show');
                document.body.classList.add('loading');

                formData.append('id', id);
                axios.post(url, formData).then(function (response) {
                    const tempBlock = document.createElement('div');
                    tempBlock.innerHTML = response.data;
                    const alert = tempBlock.querySelector('.alert'),
                        block   = document.querySelector('main');

                    if (document.querySelectorAll('.alert').length > 0) {
                        document.querySelectorAll('.alert').forEach(function (element) {
                            element.remove();
                        });
                    }

                    block.prepend(alert);
                    overlay.classList.remove('show');
                    document.body.classList.remove('loading');
                }).catch(function (error) {
                    console.error(error);
                })
            }
        });
    });
}

if (choices.length > 0) {
    initChoices(choices);
}

function jsConfirmation(jsConfirm, form = null) {
    jsConfirm.forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const actionForm = !form ? button.closest('form') : form,
                self         = this;
            Swal.fire({
                title: self.dataset.confirm === 'multi'? 'Вы уверены что хотите удалить эти записи?' : 'Вы уверены что хотите удалить эту запись?',
                icon: 'warning',
                showCancelButton: true,
                showConfirmButton:true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Да',
                cancelButtonText: 'Нет',
            }).then(function (data) {
                if(data.isConfirmed) {
                    if (form) {
                        document.body.appendChild(actionForm);
                    }
                    actionForm.submit();
                }
            })
        });
    });
}

if (jsConfirm.length > 0) {
    jsConfirmation(jsConfirm);
}

class Images extends HTMLElement
{
    constructor() {
        super();
        const input       = this.querySelector('input[type=file]');
        const container   = this.querySelector('.images-container');
        const existsImage = this.querySelectorAll('.wrapper-image');

        this.modalElement = document.getElementById('mainModal');
        this.modal        = new Modal(this.modalElement);

        this.getModalElement = function () {return this.modalElement}
        this.getContainer    = function () {return container}

        if (input) {
            input.addEventListener('change', this.changeImage.bind(this));
        }

        this.modalElement.addEventListener('hide.bs.modal', function () {
            this.querySelector('.modal-dialog').classList.remove('modal-fullscreen');
            this.querySelector('.modal-body').innerHTML = '';
            this.classList.remove('dark');
        });

        if (existsImage.length > 0) {
            this.editOptions(existsImage);
        }
    }
    editOptions(exists) {
        const self = this;

        exists.forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                const formData = new FormData(),
                    photoId    = this.dataset.photoId,
                    photoOwner = this.dataset.photoOwner,
                    productId  = this.dataset.productId,
                    categoryId = this.dataset.categoryId;
                formData.append('owner', photoOwner);
                formData.append('id', photoId);
                if (typeof productId !== 'undefined')
                    formData.append('product_id', productId);
                if (typeof categoryId !== 'undefined')
                    formData.append('category_id', categoryId);
                axios.post('/admin/photos/get-photos', formData).then(function (response) {
                    if (response.status === 200 && response.statusText === 'OK') {
                        const answer = response.data;
                        if (answer.error) {
                            console.error(answer.error);
                            return null;
                        } else {
                            self.getModalElement().classList.add('dark');
                            self.getModalElement().querySelector('.modal-dialog').classList.add('modal-fullscreen');
                            self.getModalElement().querySelector('.modal-body').innerHTML = answer;
                            self.modal.show();
                            const thumbs   = self.getModalElement().querySelectorAll('.thumb-photo'),
                                forms      = self.getModalElement().querySelectorAll('form'),
                                mainPhotos = self.getModalElement().querySelectorAll('.main-photo');
                            toggleGallery(thumbs, forms, mainPhotos);
                            submitForms(forms);

                            const closeButtons = self.getModalElement().querySelectorAll('[data-bs-dismiss="modal"]');
                            if (closeButtons.length > 0) {
                                closeButtons.forEach(function (button) {
                                    button.addEventListener('click', function (event) {
                                        event.preventDefault();
                                        event.stopPropagation();
                                        self.modal.hide();
                                    });
                                });
                            }
                        }
                    }
                }).catch(function (error) {
                    console.error(error);
                });
            })
        })
    }

    changeImage(event) {

        const files = event.currentTarget.files,
            container = this.getContainer(),
            newItems  = container.querySelectorAll('.image-item.new');

        newItems.forEach(function (item) {
            item.remove();
        });

        Array.from(files).forEach(function (file, index) {
            const reader = new FileReader();

            reader.onload = (e) => {
                const image = new Image(),
                    wrapperImage = document.createElement('div'),
                    imageItem    = document.createElement('div');

                image.src = e.target.result.toString();
                imageItem.classList.add('image-item', 'new');
                imageItem.setAttribute('style', 'max-height:142px');
                wrapperImage.classList.add('wrapper-image');
                wrapperImage.append(image);
                imageItem.append(wrapperImage);
                container.append(imageItem);
            };
            reader.readAsDataURL(files[index]);
        });
    }
}

customElements.define('up-images', Images);

class InputTags extends HTMLElement
{
    constructor() {
        super();
        const InputSelect = this.querySelector('select'),
            existsChoices = [];

        InputSelect.querySelectorAll('option').forEach(function (element) {
            if (element.getAttribute("value") !== '') {
                existsChoices.push({"value": element.getAttribute("value"), "label": element.textContent});
            }
        });

        this.init(InputSelect);
        this.getExistsChoices = function () {return existsChoices};
    }
    init(InputSelect) {
        initChoices([InputSelect]);
        this.SearchInput = this.querySelector('[name=search_terms]');
        this.SearchInput.addEventListener('keyup', this.searchTags.bind(this));
    }

    checkKey(key, code) {
        return ((key >= '0' && key <= '9') || (/[0-9a-zа-яё]+/i.test(key)) && /^Key*/i.test(code));
    }

    searchTags(event) {
        const choices = this.choices,
            searchInput = this.SearchInput;

        if (event.code === 'Enter' && this.SearchInput.value !== '') {
            const formData = new FormData(),
                meta       = {
                    "title"      : "Товары по тегу "+event.target.value,
                    "description": "На данной странице представлены товары с тегом "+event.target.value
                },
                existsChoices   = this.getExistsChoices(),
                selectedChoices = choices.getValue();

            if(
                selectedChoices.find(el => el.label === event.target.value) ||
                existsChoices.find(el => el.label === event.target.value)
            ) {
                console.warn('Дубликат');
                return null;
            }

            let lostChoices = existsChoices.map(
                function (el) {
                    if(!selectedChoices.find(selected => Number(selected.value) === Number(el.value))) {
                        return el;
                    }
                });

            formData.append('name', event.target.value);
            formData.append('meta.title', meta.title);
            formData.append('meta.description', meta.description);

            axios.post('/admin/shop/tags/create-ajax', formData).then(function (response) {
                if (response.status === 200 && response.statusText === 'OK') {
                    const answer = response.data;
                    if (answer.success) {
                        lostChoices.push({value: answer.id, label:event.target.value});
                        choices.setChoices(lostChoices, 'value', 'label', true);
                        choices.setChoiceByValue(answer.id);
                        searchInput.value = '';
                    } else {
                        console.error(answer.error);
                    }
                }
            }).catch(function (error) {
                console.error(error);
            });

        }
    }
}
customElements.define('input-tags', InputTags);

class VariantsList extends HTMLElement
{
    constructor() {
        super();
        this.variants        = this.querySelectorAll('.variant');
        this.modalElement    = document.getElementById('mainModal');
        this.modal           = new Modal(this.modalElement);
        this.getModal        = function () {return this.modal}
        this.getModalElement = function () {return this.modalElement}

        this.modalElement.addEventListener('hide.bs.modal', function () {
            this.querySelector('.modal-dialog').classList.remove('modal-fullscreen');
            this.querySelector('.modal-body').innerHTML = '';
            this.classList.remove('dark');
        });

        this.init(this.variants);
    }

    addImage(event) {
        const formData = new FormData(),
            self       = this;

        formData.append('id', event.target.dataset.variantId);

        axios.post('/admin/photos/get-variant-photos', formData).then(function (response) {

            if (response.status === 200 && response.statusText === 'OK') {
                const answer = response.data;
                if (typeof answer.error !== 'undefined') {
                    console.error(answer.error);
                    return null;
                }

                self.getModalElement().classList.add('dark');
                self.getModalElement().querySelector('.modal-body').innerHTML = answer;
                self.getModalElement().querySelector('.modal-dialog').classList.add('modal-fullscreen');
                feather.replace({ 'aria-hidden': 'true' })
                self.getModal().show();

                self.variantId = document.getElementById('modalGallery').dataset.variantId;

                const thumbs      = self.getModalElement().querySelectorAll('.thumb-photo'),
                    forms         = self.getModalElement().querySelectorAll('.photo-form'),
                    mainPhotos    = self.getModalElement().querySelectorAll('.main-photo'),
                    uploadButtons = self.getModalElement().querySelectorAll('[name="add-photos[]"]');

                if (uploadButtons.length > 0) {
                    uploadButtons.forEach(function (fileInput) {
                        fileInput.addEventListener('change', self.changeImage.bind(self));
                    });
                }
                toggleGallery(thumbs, forms, mainPhotos);
                submitForms([document.getElementById('variantForm-'+self.variantId)], true);
            }

        }).catch(function (error) {
            console.error(error);
        });
    }

    manageVariant(event) {
        console.log(event.target);
    }

    changeImage(event) {
        const files     = event.currentTarget.files,
            insertPoint = this.getModalElement().querySelector('.button-item'),
            newItems    = this.getModalElement().querySelectorAll('.thumb-item.new'),
            self        = this;

        if (newItems.length > 0) {
            newItems.forEach(function (item) {
                item.remove();
            });
        }

        Array.from(files).forEach(function (file, index) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const image = new Image(),mainImage = new Image(),wrapperImage = document.createElement('div'),
                    imageItem = document.createElement('div'),label = document.createElement('label'),
                    spanValue = document.createElement('span'),newInput = document.createElement('input'),
                    mainPhoto = document.createElement("div"),photoForm = document.createElement('div'),
                    hiddenInput = document.createElement('input'),formFloating = document.createElement('div'),
                    formFloating2 = document.createElement('div'),inputTextarea = document.createElement('textarea'),
                    inputAltTag = document.createElement('input'),labelInput = document.createElement('label'),
                    labelTextarea = document.createElement('label'),formText = document.createElement('div'),
                    submitButton  = document.createElement('button');

                submitButton.type = 'submit';
                submitButton.setAttribute('form', 'variantForm-'+self.variantId);
                submitButton.classList.add('btn', 'btn-success', 'w-100');
                submitButton.innerText = 'Сохранить';

                formText.classList.add('form-text');

                labelInput.classList.add('form-label');
                labelInput.setAttribute('for', 'newAltTag-'+index.toString());
                labelInput.innerText = 'Alt атрибут';

                labelTextarea.setAttribute('for', 'newDescription-'+index.toString());
                labelTextarea.innerText   = 'Описание изображения';

                inputAltTag.setAttribute('form', 'variantForm-'+self.variantId);
                inputAltTag.classList.add('form-control');
                inputAltTag.id          = 'newAltTag-'+index.toString();
                inputAltTag.name        = 'alt_tag['+ file.name +']';
                inputAltTag.type        = 'text';
                inputAltTag.placeholder = 'Alt атрибут';

                inputTextarea.setAttribute('form', 'variantForm-'+self.variantId);
                inputTextarea.classList.add('form-control');
                inputTextarea.id          = 'newDescription-'+index.toString();
                inputTextarea.name        = 'description['+ file.name +']';
                inputTextarea.placeholder = 'Описание изображения';

                formFloating.classList.add('form-floating', 'mb-3');
                formFloating2.classList.add('form-floating', 'mb-3');

                hiddenInput.setAttribute('form', 'variantForm-'+self.variantId);
                hiddenInput.type  = 'hidden';
                hiddenInput.name  = 'id[]';
                hiddenInput.value = index.toString();

                photoForm.id = 'new-photo_'+index;
                photoForm.classList.add('photo-form');
                photoForm.dataset.photoId = 'new-photo-'+index.toString();

                formFloating.append(inputAltTag);
                formFloating.append(labelInput);
                formFloating2.append(inputTextarea);
                formFloating2.append(labelTextarea);
                formText.append(submitButton);
                photoForm.append(hiddenInput);
                photoForm.append(formFloating);
                photoForm.append(formFloating2);
                photoForm.append(formText);

                document.getElementById('leftSide').append(photoForm);

                image.src = e.target.result.toString();
                mainImage.src = e.target.result.toString();

                mainPhoto.classList.add('main-photo');
                mainPhoto.dataset.photoId = 'new-photo-'+index;

                imageItem.classList.add('thumb-item', 'new');
                imageItem.dataset.photoId = 'new-photo-'+index;

                wrapperImage.classList.add('thumb-photo');
                wrapperImage.append(image);

                label.classList.add('variant-image-checkbox');
                spanValue.classList.add('value');

                newInput.type  = 'checkbox';
                newInput.name  = 'variantsPhoto[]';
                newInput.value = file.name;

                label.append(newInput);
                label.append(spanValue);

                mainPhoto.append(mainImage);

                imageItem.append(wrapperImage);
                imageItem.append(label);
                insertPoint.insertAdjacentElement('beforebegin', imageItem);
                document.getElementById('variantForm-' + self.variantId).insertAdjacentElement('beforebegin', mainPhoto);

                if (index+1 === files.length) {
                    const forms = self.getModalElement().querySelectorAll('.photo-form'),
                        mainPhotos = self.getModalElement().querySelectorAll('.main-photo'),
                        thumbs     = self.getModalElement().querySelectorAll('.thumb-photo');
                    toggleGallery(thumbs, forms, mainPhotos);
                    submitForms([document.getElementById('variantForm-'+self.variantId)], true);
                }
            };
            reader.readAsDataURL(files[index]);
        });
    }

    init(variants) {
        if (variants.length === 0) {
            console.error('Варианты не найдены');
            return null;
        }
        const self = this;
        variants.forEach(function (variant) {
            const addImage    = variant.querySelector('.js-variantImg'),
                manageButtons = variant.querySelector('.js-variantManage');

            addImage.addEventListener('click', self.addImage.bind(self));
            manageButtons.addEventListener('click', self.manageVariant.bind(self));
        });
    }
}
customElements.define('variant-list', VariantsList);

if (mainCategorySelect) {
    mainCategorySelect.addEventListener('change', function (event) {
        const catId = event.target.value,
            formData = new FormData;

        formData.append('id', catId);

        axios.post('/admin/shop/products/get-attributes-form', formData).then(function (response) {
            if (response.status === 200 && response.statusText === 'OK') {
                if (attributesBlock) {
                    attributesBlock.innerHTML     = response.data;
                    attributesBlock.style.display = 'block';
                }
                const choices = attributesBlock.querySelectorAll('.js-choices');
                if(choices.length > 0) {
                    initChoices(choices);
                }
            }
        }).catch(function (error) {
            console.error(error);
        });
    });
}

if (variantsBlock && attributesBlock) {
    const blockContent = variantsBlock.querySelector('.variants-container'),
        addVariantBtn  = variantsBlock.querySelector('a.js-add-variant');

    addVariantBtn.addEventListener('click', function (event) {
        event.preventDefault();
        const attributes = attributesBlock.querySelectorAll('select');
        if (attributes.length === 0) {return null}

        const formData = new FormData();
        attributes.forEach(function (element) {
            let attributeId = element.dataset.attributeId;
            if (attributeId && attributeId !== '') {
                formData.append('attributeIds[]', attributeId);
            }
        });
        axios.post('/admin/shop/products/get-variants-form', formData).then(function (response) {
            if (response.status === 200 && response.statusText === 'OK') {
                const answer = response.data;
                if (typeof answer.error === 'undefined') {
                    blockContent.innerHTML = answer;
                } else {
                    console.error(answer.error);
                }
            }

        }).catch(function (error) {
            console.error(error);
        })
    });
}

if (createMenuButton) {
    createMenuButton.addEventListener('click', function (event) {
        event.preventDefault();
        const myModal = document.getElementById('mainModal'),
            modal = new Modal(myModal);

        myModal.addEventListener('hidden.bs.modal', event => {
            myModal.querySelector('.modal-body').innerHTML = '';
        });

        axios.post('/admin/ajax/get-form-menu').then(function (response) {
            if (response.status === 200 && response.statusText === 'OK') {
                myModal.querySelector('.modal-body').innerHTML = response.data;
                modal.show();
            }
        }).catch(function (error) {
            console.error(error)
        })
    })
}

if (editMenuButton.length > 0) {
    editMenuButton.forEach(function (element) {
        element.addEventListener('click', function (event) {
            event.preventDefault();
            const myModal = document.getElementById('mainModal'),
                modal     = new Modal(myModal),
                formData  = new FormData();

            myModal.addEventListener('hidden.bs.modal', event => {
                myModal.querySelector('.modal-body').innerHTML = '';
            });

            formData.append('menu_id', this.dataset.menuId);

            axios.post('/admin/ajax/get-form-menu', formData).then(function (response) {
                if (response.status === 200 && response.statusText === 'OK') {
                    myModal.querySelector('.modal-body').innerHTML = response.data;
                    modal.show();
                }

            }).catch(function (error) {
                console.error(error)
            })
        });
    });
}

class MenuManage extends HTMLElement
{
    constructor() {
        super();
        this.addButton      = this.querySelector('.js-add-item');
        this.itemsContainer = this.querySelector('.menu-items-container');

        if (this.addButton) {
            this.addItem(this.addButton);
        }
        this.initSortable(this.itemsContainer.querySelectorAll('.list-group'));

        this.initControlButtons();

        document.addEventListener('click', function (event) {
            if (!event.target.classList.contains('answer-item') && !event.target.classList.contains('menu-item-input')) {
                const containers = document.querySelectorAll('.answer-container');
                if (containers.length > 0) {
                    containers.forEach(function (container) {
                        const containerBox = container.closest('.list-group-item'),
                            itemInput      = containerBox.querySelector('input');
                        if (itemInput) {
                            itemInput.value = '';
                        }
                        container.remove();
                    })
                }
            }
            if (!event.target.classList.contains('edit-item-title') && !event.target.classList.contains('form-control')) {
                const newInputs = document.querySelectorAll('.new-title.show');
                if (newInputs.length > 0) {
                    newInputs.forEach(function (newInput) {
                        newInput.classList.remove('show');
                    });
                }
            }
        });
    }

    initElements() {
        this.addButton = this.querySelector('.js-add-item');
        if (this.addButton) {
            this.addItem(this.addButton);
        }
        this.initSortable(this.itemsContainer.querySelectorAll('.list-group'));
        this.initControlButtons();
    }

    setSortAttributes(items, parent = null) {
        const self = this;
        Array.from(items).forEach(function (element, index) {
            const currentId = element.dataset.id,
                children    = element.querySelector('.list-group'),
                sortName    = 'input[name="items[' + currentId + '][sort]"]',
                parentName  = 'input[name="items[' + currentId + '][parent]"]',
                inputSort   = element.querySelector(sortName);

            element.dataset.sort = index.toString();
            inputSort.value      = index.toString();

            const inputParent = element.querySelector(parentName);

            if (parent) {
                inputParent.value          = parent.toString();
                element.dataset.itemParent = parent;
            } else {
                inputParent.value          = '0';
                element.dataset.itemParent = '0';
            }

            if (children.children.length > 0) {
                self.setSortAttributes(children.children, currentId);
            }
        });
    }

    selectListener(event) {
        if (!event.target.classList.contains('answer-item')) {
            return false;
        }
        const box = event.target.closest('.list-group-item'),
            inputHiddenType = document.createElement('input'),
            inputHiddenId   = document.createElement('input');

        if (box && typeof box !== 'undefined') {

            if (event.target.dataset.type === 'external' && !this.isValidHttpUrl(event.target.textContent.replace(event.target.querySelector('span').textContent, ''))) {
                const errorBlock = document.createElement("span");

                errorBlock.classList.add('invalid-feedback');
                errorBlock.textContent = 'Неверный URL адрес!';

                box.querySelector('input[type=text]').classList.add('is-invalid');
                box.querySelector('input[type=text]').nextSibling.after(errorBlock);

                return;
            }

            inputHiddenType.type  = 'hidden';
            inputHiddenType.name  = 'items[' + box.dataset.id + '][type]';
            inputHiddenType.value = event.target.dataset.type;

            inputHiddenId.type  = 'hidden';
            inputHiddenId.name  = 'items[' + box.dataset.id + '][item_id]';
            inputHiddenId.value = event.target.dataset.id;

            box.append(inputHiddenType);
            box.append(inputHiddenId);

            box.querySelector('input[type=text]').value                   = event.target.textContent.replace(event.target.querySelector('span').textContent, '');
            box.querySelector('input[type=text]').placeholder             = event.target.querySelector('span').textContent;
            box.querySelector('input[type=text]').nextSibling.textContent = event.target.querySelector('span').textContent;

            event.target.parentElement.removeEventListener('click', this.selectListener);
            event.target.parentElement.remove();
        }

    }

    arrowHandler(event) {
        let answerList = event.target.closest('.list-group-item').querySelectorAll('.answer-item'),
            index      = Array.from(answerList).indexOf(this.querySelector('.answer-item.active'));
        if (event.key === 'ArrowDown') {
            if (typeof answerList[++index] !== 'undefined') {
                answerList[index].classList.add('active');
            }
            if (typeof answerList[--index] !== 'undefined') {
                answerList[index].classList.remove('active');
            }
        }
        if (event.key === 'ArrowUp') {
            if (typeof answerList[--index] !== 'undefined') {
                answerList[index].classList.add('active');
            }
            if (typeof answerList[++index] !== 'undefined') {
                answerList[index].classList.remove('active');
            }
        }
    }

    isValidHttpUrl(string) {
        let url;
        try {
            url = new URL(string);
        } catch (_) {
            return false;
        }
        return url.protocol === 'http:' || url.protocol === 'https:';
    }

    enterHandler(event) {
        const container       = event.target.closest('.list-group-item'),
            element           = container.querySelector('.answer-item.active');

        if (!element) {return;}

        const type            = element.dataset.type,
            typeName          = element.querySelector('span').textContent,
            title             = element.textContent.replace(typeName, ''),
            itemId            = element.dataset.id,
            id                = container.dataset.id,
            hiddenInputType   = document.querySelector('input[name="items[' + id + '][type]"]') ??
                document.createElement('input'),
            hiddenInputItemId = document.querySelector('input[name="items[' + id + '][item_id]"]') ??
                document.createElement('input');

        hiddenInputType.type  = 'hidden';
        hiddenInputType.name  = 'items['+id+'][type]';
        hiddenInputType.value = type;

        hiddenInputItemId.type  = 'hidden';
        hiddenInputItemId.name  = 'items['+id+'][item_id]';
        hiddenInputItemId.value = itemId;

        if (type === 'external' && !this.isValidHttpUrl(event.target.value)) {
            const errorBlock     = document.createElement("span");
            errorBlock.classList.add('invalid-feedback');
            errorBlock.textContent = 'Неверный URL адрес!';
            event.target.classList.add('is-invalid');
            event.target.nextSibling.after(errorBlock);
            return;
        }

        event.target.value              = title;
        event.target.placeholder        = typeName;
        event.target.nextSibling.textContent = typeName;

        container.append(hiddenInputType);
        container.append(hiddenInputItemId);

        container.querySelector('.answer-container').remove();
    }

    renderAnswer(answer, event) {
        const targetContainer = event.target.closest('.list-group-item'),
            answerContainer   = targetContainer.querySelector('.answer-container') ?? document.createElement('div'),
            self              = this,
            form              = event.target.closest('form');

        form.addEventListener('keydown', function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
        });

        answerContainer.classList.add('answer-container');

        answerContainer.innerHTML = '';

        answer.forEach(function (item) {
            const answerItem = document.createElement('div'),
                answerType   = document.createElement('span');

            answerItem.classList.add('answer-item');
            answerItem.dataset.type = item.type;
            answerItem.dataset.id   = item.model_id;
            answerItem.textContent  = item.name ?? item.title;
            answerType.textContent  = item.model;
            answerItem.append(answerType);

            if (!answerContainer.querySelector('#'+self.type+item.id)) {
                answerContainer.append(answerItem);
            }
        });
        if (!targetContainer.querySelector('.answer-container')) {
            targetContainer.append(answerContainer);
            targetContainer.removeEventListener('click', self.selectListener.bind(self),false);
            targetContainer.addEventListener('click', self.selectListener.bind(self), false);
        } else {
            if (event.code === 'ArrowDown' || event.code === 'ArrowUp') {
                this.arrowHandler(event);
            }
            if (event.keyCode === 13) {
                this.enterHandler(event);
            }
        }
    }

    initSortable (sortables) {
        const self = this;
        for (let i = 0; i < sortables.length; i++) {
            new Sortable(sortables[i], {
                group: 'nested',
                animation: 150,
                fallbackOnBody: true,
                swapThreshold: 0.65,
                onSort: self.updateSort.bind(self),
            });
        }
    }

    removeErrors() {
        const errorInputs = this.querySelectorAll('.is-invalid'),
            errorBlocks  = this.querySelectorAll('.invalid-feedback');

        if (errorInputs.length > 0) {
            errorInputs.forEach(function (errorInput) {
                errorInput.classList.remove('is-invalid');
            });
        }
        if (errorBlocks.length > 0) {
            errorBlocks.forEach(function (errorBlock) {
                errorBlock.remove();
            });
        }
    }

    find(event) {
        const formData = new FormData,
            self       = this;
        formData.append('query', event.target.value);

        if (event.target.value === '') {
            this.removeErrors();
        }

        if (event.code !== 'ArrowDown' && event.code !== 'ArrowUp' && event.keyCode !== 13) {
            axios.post('/admin/navigations/find',formData).then(function (response) {
                if (response.status === 200 && response.statusText === 'OK') {
                    const answer = response.data;
                    if (Array.isArray(answer) && answer.length > 0) {
                        self.renderAnswer(answer.slice(0, 10), event);
                        self.removeErrors();
                    } else if (answer === '' || (Array.isArray(answer) && answer.length === 0)) {
                        const targetContainer = event.target.closest('.list-group-item'),
                            answerContainer   = targetContainer.querySelector('.answer-container');
                        if (answerContainer && event.target.value === '') {
                            answerContainer.remove();
                        } else if (event.target.value !== '') {
                        const customItemAnswer = {
                                'name': event.target.value,
                                'type': event.target.value === '#' ? 'separator' : 'external',
                                'model': event.target.value === '#' ? 'Разделитель' : 'Внешний',
                                'model_id':0
                            },
                            customAnswer = [customItemAnswer];
                            self.renderAnswer(customAnswer, event);
                        }
                    }
                }
            }).catch(function (error) {
                console.error(error);
            });
        } else if (event.keyCode === 13) {
            this.enterHandler(event);
        } else {
            this.arrowHandler(event);
        }
    }

    updateSort() {
        const draggableContainer = document.getElementById('draggable');
        if (draggableContainer.children.length > 0) {
            this.setSortAttributes(draggableContainer.children);
        }
    }

    initControlButtons() {
        const buttons = this.itemsContainer.querySelectorAll('.control-item-buttons button');
        buttons.forEach(function (button) {
            const tooltipBtn = new Tooltip(button);

            if (button.classList.contains('delete-item') || button.classList.contains('remove-item-image')) {
                const form = document.createElement('form'),
                    inputMethod = document.createElement('input'),
                    inputCSRF   = document.createElement('input');

                form.action = button.classList.contains('delete-item') ?
                    '/admin/navigations/menu-item-delete/'+button.dataset.itemId :
                    '/admin/navigations/menu-item-delete-image/'+button.dataset.itemId;
                form.method       = 'POST';
                inputMethod.type  = 'hidden';
                inputMethod.name  = '_method';
                inputMethod.value = 'DELETE';

                inputCSRF.type  = 'hidden';
                inputCSRF.name  = '_token';
                inputCSRF.value = document.querySelector('meta[name="csrf-token"]').content;

                form.append(inputMethod);
                form.append(inputCSRF);

                jsConfirmation([button], form);
            } else {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    tooltipBtn.hide();
                    if (button.classList.contains('edit-item-title')) {
                        const newTitle = event.target.closest('.control-item-buttons').querySelector('.new-title');
                        newTitle.classList.add('show');
                    }
                    if (button.classList.contains('add-item-image')) {
                        window.open('/file-manager/fm-button', 'fm', 'width=1400,height=800');
                        inputId = 'navImage'+button.dataset.itemId
                    }
                });
            }
        });
    }

    addItem(button) {
        let self = this;
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const wrapperItem      = document.createElement('div'),
                wrapperDraggable   = document.createElement('div'),
                containerDraggable = document.createElement('div'),
                inputItemTitle     = document.createElement('input'),
                inputHiddenId      = document.createElement('input'),
                inputHiddenSort    = document.createElement('input'),
                inputHiddenParent  = document.createElement('input'),
                labelItemTitle     = document.createElement('label'),
                listDraggable      = self.itemsContainer.querySelector('#draggable');

            let length = self.itemsContainer.querySelectorAll('.menu-item-input').length;

            if (self.itemsContainer.querySelectorAll('.list-group-item').length > 0) {
                const lengthArray = [];
                self.itemsContainer.querySelectorAll('.list-group-item').forEach(function (item) {
                    lengthArray.push(item.dataset.id);
                });
                length = Math.max(...lengthArray);
            }

            inputItemTitle.classList.add('form-control', 'menu-item-input');
            inputItemTitle.id          = 'menuItemInput-' + (length + 1);
            inputItemTitle.name        = 'items[' + (length + 1) + '][title]';
            inputItemTitle.placeholder = '-= Начните набирать текст =-';
            inputItemTitle.type        = 'text';
            inputItemTitle.setAttribute('autocomplete', 'off');

            wrapperItem.classList.add('form-floating');

            labelItemTitle.classList.add('form-label');
            labelItemTitle.textContent = '-= Начните набирать текст =-';
            labelItemTitle.setAttribute('for', 'menuItemInput-'+(length+1));

            containerDraggable.classList.add('list-group', 'nested-sortable');

            wrapperDraggable.classList.add('list-group-item', 'nested-'+(length+1));
            wrapperDraggable.dataset.id   = (length + 1).toString();
            wrapperDraggable.dataset.sort = length.toString();

            inputHiddenParent.type  = 'hidden';
            inputHiddenParent.name  = 'items[' + (length + 1) + '][parent]';
            inputHiddenParent.value = '0';

            inputHiddenSort.type  = 'hidden';
            inputHiddenSort.name  = 'items[' + (length + 1) + '][sort]';
            inputHiddenSort.value = (length + 1).toString();

            inputHiddenId.type  = "hidden";
            inputHiddenId.name  = 'items[' + (length +1) + '][id]';
            inputHiddenId.value = (length + 1).toString();

            wrapperItem.append(inputItemTitle);
            wrapperItem.append(labelItemTitle);
            wrapperItem.append(inputHiddenId);
            wrapperItem.append(inputHiddenSort);
            wrapperItem.append(inputHiddenParent);

            wrapperDraggable.append(wrapperItem);
            wrapperDraggable.append(containerDraggable);
            listDraggable.append(wrapperDraggable);

            inputItemTitle.addEventListener('keyup', self.find.bind(self));

            const nestedSortables = self.itemsContainer.querySelectorAll('.list-group');

            self.initSortable(nestedSortables);
        });
    }
}
customElements.define('menu-manage', MenuManage);

if (addNavItems.length > 0) {
    addNavItems.forEach(function (element) {
        element.addEventListener('click', function (event) {
            event.preventDefault();
            const formData     = new FormData,
                itemsContainer = document.querySelector('.menu-items-container');

            formData.append('menu_id', this.dataset.menuId);

            axios.post('/admin/ajax/get-form-menu-items', formData).then(function (response) {
                if (response.status === 200 && response.statusText === 'OK') {
                    itemsContainer.innerHTML = response.data;
                    itemsContainer.parentElement.initElements();
                    if (itemsContainer.querySelector('#draggable').childElementCount > 0) {
                        feather.replace({ 'aria-hidden': 'true' });
                    }
                }
            }).catch(function (error) {
                console.error(error);
            });
        });
    });
}
