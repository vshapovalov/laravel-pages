<template>
    <div class="">
        <div class="admin-header">
            <div class="logo">
                <a href="javascript:;">Админ.панель</a>
            </div>
            <div class="header-content"></div>
        </div>
        <div class="admin-container">
            <div class="sidebar">
                <menu-item key="menuItem.name" v-for="menuItem in menu" @selected="emitMenuAction" :item="menuItem" ></menu-item>
            </div>
            <div id="workspace" class="content"></div>
        </div>
    </div>
</template>

<script>

    import MenuItem from './menu-item.vue';
    import MediaLibrary from './../media/library';
    import CrudUtils from './../crud/utils';
    import CrudEditor from './../crud/editor';
    import CrudEditPanel from './../crud/editpanel';
    import FormBehaviorTypes from './../utils/types';

    // TODO: check for auth type

    export default {
        name: 'admin-manager',
        data: function () {
            return {
                activeInstance: undefined,
                cruds: {},
                menu: [],
                additionalComponents: [],
                additionalComponentsLoaded: 0,
                selectedMenuItem: null
            }
        },
        components:{
            'menu-item': MenuItem
        },
        methods:{
            closeActiveInstance(){
                if (this.activeInstance) {
                    this.activeInstance.close();
                }
            },

            setActiveInstance(instance){
                this.closeActiveInstance();
                this.activeInstance = instance;
            },

            mountActiveInstance(){
                if (this.activeInstance) this.mountInstance(this.activeInstance);
            },

            mountInstance(instance){
                if (instance){
                    let elemetId = 'component' + CrudUtils.randomInteger(1, 10000);

                    $(AdminManager.getWorkspaceSelector()).append(`<div id="${elemetId}"></div>`);

                    instance.show(elemetId);
                }
            },

            emitMenuAction(menuItem){
                if (this.selectedMenuItem) {
                    this.$set(this.selectedMenuItem, 'active', false);
                }

                this.selectedMenuItem = menuItem;
                this.$set(menuItem, 'active', true);

                Bus.$emit(menuItem.action, menuItem.action, menuItem.caption);

            },
            mountEditPanel(crud, item,fields, onSave = null, onCancel = null){
                let instance = new CrudEditPanel(null, crud, item,fields, onSave, onCancel);
                Bus.$emit('admin:instance:mount', instance, false);
            },
            mountMediaLibrary(){
                let instance = new MediaLibrary(null, FormBehaviorTypes.BROWSE, null ,null, null);
                Bus.$emit('admin:instance:mount', instance);
            },
            mountCrud(action){
                let crudCode = action.split(':')[1];

                let crud = AdminManager.getCrud(crudCode);

                let instance = new CrudEditor(null, crud,  FormBehaviorTypes.BROWSE, null, null, null);
                Bus.$emit('admin:instance:mount', instance);
            },
            onInstanceMounted(instance, freeActiveInstance = true){
                if (freeActiveInstance){
                    this.closeActiveInstance();
                    this.setActiveInstance(instance);
                }

                this.mountInstance(instance);
            },
            onUrlRender(url){

            },
            onUrlGo(url){
                window.location = url;
            },

            onConfigLoaded(config){
                this.cruds = config.list;
                this.menu.splice(0,0,...config.menu);
                this.additionalComponents.splice(0,0,...config.components);
                this.registerDefaultComponents();
                if (this.additionalComponents.length > 0){
                    this.registerUserComponents()
                        .then(()=>{ this.mountDefaultComponent() });
                } else {
                    this.mountDefaultComponent();
                }

            },

            mountDefaultComponent(){
                let defaultMenu = _.first(_.filter(this.menu, (m)=>m.default));
                if (defaultMenu){
                    this.emitMenuAction(defaultMenu);
                }
            },

            registerDefaultComponents(){
                Bus.$on('medialibrary:mount', this.mountMediaLibrary);
                Bus.$on('editpanel:mount', this.mountEditPanel);

                _.each(this.cruds, (crud)=>{
                    Bus.$on(`crud:${crud.code}:mount`, this.mountCrud);
                });
            },
            registerUserComponents(){
                return new Promise((resolve, reject)=>{
                    let body = document.querySelector('body');

                    _.each(this.additionalComponents, (cmp)=>{
                        let scriptEl = document.createElement('script');
                        body.appendChild(scriptEl);
                        scriptEl.setAttribute('type', 'text/javascript');
                        scriptEl.setAttribute('async', true);
                        scriptEl.onload = ()=>{ if (++this.additionalComponentsLoaded) resolve()};
                        scriptEl.setAttribute('src', cmp.path);
                    });
                });

            }
        },
        beforeMount(){

            // register admin manager events
            Bus.$on('admin:instance:mount', this.onInstanceMounted);
            Bus.$on('admin:url:render', this.onUrlRender);
            Bus.$on('admin:url:go', this.onUrlGo);
        },
        mounted(){
            this.$root.$on('config:loaded', this.onConfigLoaded);
        }
    }
</script>
