import VueMediaLibrary from './components/media-library.vue';

export default class MediaLibrary {
    constructor(selector, type, onPick, onCancel, crudField){
        this.selector = selector;
        this.type = type;
        this.onPick = onPick;
        this.onCancel = onCancel;
        this.crudField = crudField;


    }

    createLibrary(){
        let _this = this;

        this.library =  new Vue({
            template: '<media-library id="' + _this.selector + '" :active="active" :type="type" :crud-field="crudField" @pick="onPick" @cancel="onCancel"></media-library>',
            data: {
                type: _this.type,
                active: true,
                crudField: _this.crudField
            },
            watch: {
                active: {
                    handler(val, oldVal){
                        if (!val) {
                            this.closeLibrary();
                        }
                    }
                }
            },
            components: {
                'media-library': VueMediaLibrary
            },
            methods: {
                closeLibrary(){

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

                }
            }
        });
    }

    show(elementId){

        if (elementId){
            this.selector = elementId;
        }

        this.createLibrary();

        this.library.$mount('#' + this.selector);

        return this.library;
    }

    close(){
        this.library.active = false;
    }
}
