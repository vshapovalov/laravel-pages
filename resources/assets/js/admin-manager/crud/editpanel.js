import VueCrudEditpanel from './components/crud-editpanel.vue';

export default class CrudEditPanel {
    constructor(crudSelector, crud, itemId, onSave, onCancel){

        this.crud = crud;
        this.crudSelector = crudSelector;
        this.fields = crud.fields;
        this.onSave = onSave;
        this.itemId = itemId;
        this.onCancel = onCancel;
    }

    createEditor(){
        let _this = this;

        this.editor =  new Vue({
            template: '<crud-editpanel id="' + _this.crudSelector + '" :active="active" :crud="crud" ' +
            '@save="onSave" @cancel="onCancel" :item-id="itemId"></crud-editpanel>',
            data: {
                crud: _this.crud,
                active: true,
                itemId: _this.itemId,
                fields: _this.fields,
            },
            components: {
                'crud-editpanel': VueCrudEditpanel
            },
            watch:{
                active: {
                    handler: function(val, oldVal){
                        if (!val) {
                            this.closeCrud();
                        }
                    }
                }
            },
            methods: {
                closeCrud(){
                    this.$nextTick(()=>{
                        this.$destroy();
                    });
                },
                onSave(item){
                    if (_this.onSave) _this.onSave(item);

                    this.active = false;
                },
                onCancel(){
                    if (_this.onCancel) { _this.onCancel(); }
                    this.active = false;
                },

            }
        });
    }

    getData(){
        return this.editor.data;
    }

    show(elementId){

        if (elementId){
            this.crudSelector = elementId;
        }


        this.createEditor();

        this.editor.$mount('#'+ this.crudSelector);

        return this.editor;
    }

    close(){
        this.editor.active = false;
    }
}
