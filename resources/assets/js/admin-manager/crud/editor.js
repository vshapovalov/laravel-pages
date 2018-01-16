import VueCrudEditor from './components/crud-editor.vue';

export default class CrudEditor {
    constructor(crudSelector, crud, editorType, onPick, onCancel, onGetItem){

        this.crud = crud;
        this.crudSelector = crudSelector;
        this.editorType = editorType;
        this.onPick = onPick;
        this.onCancel = onCancel;
        this.inputItem = onGetItem ? onGetItem() : undefined;
    }

    createEditor(){
        let _this = this;

        this.editor =  new Vue({
            template: '<crud-editor id="' + _this.crudSelector + '" :active="active" :crud="crud" :editor-type="editorType" @pick="onPick" @cancel="onCancel" :item="item"></crud-editor>',
            data: {
                crud: _this.crud,
                editorType: _this.editorType,
                active: true,
                item: _this.inputItem
            },
            components: {
                'crud-editor': VueCrudEditor
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
                onPick(value){
                    if (_this.onPick) { _this.onPick(value); }
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
