/**
 * Created by dr_sharp on 21.09.2017.
 */
window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');

window.CSRF_TOKEN = token.content;

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}

window._ = require('lodash');

require('jquery-mask-plugin');
require('datetimepicker');
require('datetimepicker/dist/DateTimePicker.css');

window.dropzone = require('dropzone');
require('toastr/build/toastr.min.css');
window.toastr = require('toastr');
window.slugify = require('slugify');

window.Vue = require('vue');

window.Bus = new Vue({
    data: {

    },
    created(){
        console.log('bus created');
    }
});

Vue.component('admin-manager', require('./admin-manager/components/admin-manager.vue'));
Vue.component('crud-table', require('./admin-manager/crud/components/crud-table.vue'));
Vue.component('crud-editpanel', require('./admin-manager/crud/components/crud-editpanel.vue'));
Vue.component('crud-tree', require('./admin-manager/crud/components/crud-tree.vue'));
Vue.component('crud-textbox', require('./admin-manager/crud/components/crud-textbox.vue'));
Vue.component('crud-checkbox', require('./admin-manager/crud/components/crud-checkbox.vue'));
Vue.component('crud-textarea', require('./admin-manager/crud/components/crud-textarea.vue'));
Vue.component('crud-colorbox', require('./admin-manager/crud/components/crud-colorbox.vue'));
Vue.component('crud-datepicker', require('./admin-manager/crud/components/crud-datepicker.vue'));
Vue.component('crud-richedit', require('./admin-manager/crud/components/crud-richedit.vue'));
Vue.component('crud-dropdown', require('./admin-manager/crud/components/crud-dropdown.vue'));
Vue.component('crud-relation-one', require('./admin-manager/crud/components/crud-relation-one.vue'));
Vue.component('crud-relation-many', require('./admin-manager/crud/components/crud-relation-many.vue'));
Vue.component('crud-image', require('./admin-manager/crud/components/crud-image.vue'));
Vue.component('crud-tree-item', require('./admin-manager/crud/components/crud-tree-item.vue'));
Vue.component('crud-caption', require('./admin-manager/crud/components/crud-caption.vue'));

// Vue.component('', require('./admin-manager/crud/components/.vue'));

import AdminManager from './admin-manager';

$(document).ready(function(){
    "use strict";

    window.AdminManager = new AdminManager('#admin-manager');
});

