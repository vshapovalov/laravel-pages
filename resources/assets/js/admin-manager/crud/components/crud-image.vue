<template>
    <div class="">
        <div>
            <a class="button is-primary" @click.stop.prevent="showLibrary">Выбрать</a>
        </div>
        <div v-for="(image, index) in images" :class="{'box': field.additional && field.additional.type === 'image', 'is-inline-block': field.additional && field.additional.type === 'image'}">

            <div v-if="field.additional && field.additional.type === 'image'" class="image is-64x64" @click.stop.prevent="showModal(image)">
                <img :src="fullPath(image)" :alt="image">
            </div>
            <a v-else :href="fullPath(image)">{{ image }}</a>
            <a class="button is-danger is-small" @click.stop.prevent="deleteImage(index)">удалить</a>
        </div>
        <div v-show="images.length === 0"><span>Нет {{ field.additional && field.additional.type === 'image' ? 'изображений' : 'файлов' }}</span></div>
        <div class="modal" :class="{'is-active': modalForm.showing }">
            <div class="modal-background" @click.stop.prevent="cancelModal"></div>
            <div class="modal-content">
                <p class="image">
                    <img :src="fullPath(modalForm.image)" alt="">
                </p>
            </div>
            <button class="modal-close is-large" aria-label="close" @click.stop.prevent="cancelModal"></button>
        </div>
    </div>
</template>

<script>

    import LibraryBuilder from './../../media/builder';
    import FormBehaviorTypes from './../../utils/types';

    export default {
        name: 'crud-image',
        model:{
            prop: "value",
            event: "change"
        },
        props: ['field', 'value'],
        data: function () {
            return {
                images: [],
                modalForm: {
                    image: '',
                    showing: false
                }
            }
        },
        computed: {
            mode(){
                let mode = 'multi';

                if(this.field.additional && this.field.additional.mode) {
                    mode = this.field.additional.mode;
                }

                return mode;

            }
        },
        watch: {
            value: {
                handler(val, oldVal){
                    this.parseVal(val);
                }
            }
        },
        methods: {
            /********************************* modal ***********************************/
            showModal(image){
                this.modalForm.image = image;
                this.modalForm.showing = true;
            },
            cancelModal(){
                this.modalForm.showing = false;
                this.modalForm.image = '';
            },

            /********************************* end modal ***********************************/

            fullPath(item){
                return baseUrl + item;
            },

            parseVal(val){
                this.images = [];

                if (this.mode === 'single'){
                    if (val){
                        this.images.splice(0,0,val);
                    }

                } else {
                    if (val){

                        let parsedVal;

                        try {
                            parsedVal = JSON.parse(val);
                        } catch(e){
                        }

                        if (parsedVal) this.images = parsedVal;
                    }
                }
            },

            emitChange(){
                if (this.mode === 'single'){
                    if (this.images.length>0){
                        this.$emit('change', _.first(this.images));
                    } else {
                        this.$emit('change', '');
                    }
                } else {
                    this.$emit('change', JSON.stringify(this.images));
                }
            },
            onPick(items){

                if (items && items.length>0){
                    if (this.mode === 'single'){
                        this.images = [_.first(items)];
                    } else {
                        this.images.splice(this.images.length, 0, ...items);
                        this.images = _.uniq(this.images);
                    }

                    this.emitChange();
                }
            },
            showLibrary(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                let formBehavior = FormBehaviorTypes.PICK;

                if (this.mode === "multi") formBehavior = FormBehaviorTypes.PICK_MANY;

                console.log(this.field);

                new LibraryBuilder(formBehavior)
                    .setCrudField(this.field)
                    .onPick(this.onPick)
                    .build()
                    .show();
            },
            deleteImage(index){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.images.splice(index, 1);
                this.emitChange();
            }
        },
        beforeMount(){
            this.parseVal(this.value);
        },

        mounted() {

        }
    }
</script>
