<template>
    <div class="">
        <div class="">
            <div class="control">
                <a v-show="isButtonVIsible('pick')" class="button is-success" @click.prevent.stop="pickItems">Выбрать</a>
                <a v-show="isButtonVIsible('add')" class="button is-primary" @click.prevent.stop="addItem">Добавить</a>
                <a v-show="isButtonVIsible('delete_all')" class="button is-danger" @click.prevent.stop="delAllItems">Удалить все</a>
            </div>
        </div>
        <crud-table :items="items" :type="crudTypes.PICK_MANY" :crud="crud" @delete="deleteItem" @edit="editItem"
                    :row-actions="tableActions" :pivot-fields="field.relation.pivot"></crud-table>

        <div v-if="modalForm.active" class="modal is-active">
            <div class="modal-background"></div>
            <div class="modal-card" style="height: 100%;">
                <header class="modal-card-head">
                    <p class="modal-card-title">Редактирование</p>
                    <button class="delete" aria-label="close" @click="modalCancel"></button>
                </header>
                <section class="modal-card-body">
                    <div class="tabs">
                        <ul>
                            <li v-for="tab in pivotTabs" :class="{'is-active' : activeTab == tab}"><a @click="setActiveTab(tab)">{{ tab }}</a></li>
                        </ul>
                    </div>

                    <div v-for="tab in pivotTabs" v-show="activeTab == tab">
                        <div class="field " v-for="field in pivotFields" v-if="field.tab == tab || (!field.tab & tab == 'Основные параметры')">
                            <label class="label">{{ field.caption }}</label>
                            <div class="control">
                                <crud-textbox v-if="field.type === fieldTypes.TEXTBOX" v-model="pivotRow[field.name]" :field="field"></crud-textbox>
                                <crud-checkbox v-if="field.type === fieldTypes.CHECKBOX" v-model="pivotRow[field.name]" :field="field"></crud-checkbox>
                                <crud-textarea v-if="field.type === fieldTypes.TEXTAREA" v-model="pivotRow[field.name]" :field="field"></crud-textarea>
                                <crud-datepicker v-if="field.type === fieldTypes.DATEPICKER" v-model="pivotRow[field.name]" :field="field"></crud-datepicker>
                                <crud-colorbox v-if="field.type === fieldTypes.COLORBOX" v-model="pivotRow[field.name]" :field="field"></crud-colorbox>
                                <crud-image v-if="field.type === fieldTypes.IMAGE" v-model="pivotRow[field.name]" :field="field"></crud-image>
                                <crud-dropdown v-if="field.type === fieldTypes.DROPDOWN" v-model="pivotRow[field.name]" :field="field"></crud-dropdown>
                                <!--<crud-relation-one-->
                                    <!--v-else-if="(field.type === fieldTypes.RELATION && (field.relation.type === relationTypes.BELONGS_TO ))"-->
                                    <!--v-model="pivotRow[field.name]" :crud-code="field.relation.crud" :field="field" :item="pivotRow"></crud-relation-one>-->
                            </div>
                        </div>
                    </div>

                </section>
                <footer class="modal-card-foot">
                    <button class="button" @click="modalCancel">Закрыть</button>
                </footer>
            </div>
        </div>

    </div>
</template>

<script>
    import CrudUtils from './../utils';
    import CrudBuilder from './../builder';
    import CrudTypes from './../../utils/types';
    import FieldTypes from './../utils/field-types';
    import RelationTypes from './../utils/relation-types';

    /*** components ***/
    import VueCrudTextbox from './crud-textbox.vue';
    import VueCrudCheckbox from './crud-checkbox.vue';
    import VueCrudTextarea from './crud-textarea.vue';
    import VueCrudColorbox from './crud-colorbox.vue';
    import VueCrudDatepicker from './crud-datepicker.vue';
    import VueCrudRichedit from './crud-richedit.vue';
    import VueCrudTable from './crud-table.vue';
    import VueCrudDropdown from './crud-dropdown.vue';
    import VueCrudEditPanel from './crud-editpanel.vue';
    import VueCrudImage from './crud-image.vue';

    export default {
        components: {
            'crud-table': VueCrudTable,
            'crud-textbox': VueCrudTextbox,
            'crud-checkbox': VueCrudCheckbox,
            'crud-textarea': VueCrudTextarea,
            'crud-colorbox': VueCrudColorbox,
            'crud-datepicker': VueCrudDatepicker,
            'crud-dropdown': VueCrudDropdown,
            'crud-image': VueCrudImage,
            'crud-editpanel': VueCrudEditPanel
        },
        name: 'crud-relation-many',
        model: {
            prop: "items",
            event: "change"
        },
        props: {
            items: { type: Array },
            field: {
                type: Object
            },
            item: {}
        },
        data: function () {
            return {
                crud: {},
                activeTab: '',
                pivotRow: {},
                modalForm: {
                    active : false,
                    onSuccess: null
                },
            }
        },
        computed: {
            relationTypes(){
                return RelationTypes;
            },

            crudTypes(){
                return CrudTypes;
            },
            pivotFields(){
                return _.filter(this.field.relation.pivot, (f)=> f.visibility && _.some(f.visibility,(v)=>v==='edit'));
            },
            tableActions(){

                let actions = ['delete', 'edit'];

                return actions;
            },
            pivotTabs(){
                return _.keys(_.groupBy(this.pivotFields, (f)=> f.tab ? f.tab : "Основные параметры"));
            },
            fieldTypes(){
                return FieldTypes;
            },
            buttons(){
                return this.field.additional && this.field.additional.buttons ? this.field.additional.buttons : ['add' ,'pick', 'delete_all']
            }
        },
        methods: {

            isButtonVIsible(btn){
                return _.find(this.buttons, b=>b===btn );
            },

            /***************************************** tabs ***************************/

            setActiveTab(tabName){
                this.activeTab = tabName;
            },


            /***************************************** end tabs ***************************/

            /***************************************** modal edit form ***************************/

            showModalForm(onSuccess){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.activeTab = this.pivotTabs[0];
                this.modalForm.onSuccess = onSuccess;
                this.modalForm.active = true;
            },
            modalSuccess(){
                if (this.modalForm.onSuccess) this.modalForm.onSuccess();
                this.modalCancel();
            },
            modalCancel(){
                this.modalForm.onSuccess = null;
                this.modalForm.active = false;
            },

            /***************************************** end modal edit form ***************************/

            addItem(){
                Bus.$emit('editpanel:mount', this.crud, null, this.saveItem);
            },

            saveItem(item){
               this.mergedItems([item]);
            },

            delAllItems(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.emitChanges([]);
            },
            deleteItem(item){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                let items = _.filter(this.items, (i) => i[this.crud.id] !== item[this.crud.id]);
                this.emitChanges(items);
            },
            editItem(item){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                if (this.field.relation.pivot.length){
                    this.pivotRow = item.pivot;
                    this.showModalForm();
                } else {

                    Bus.$emit('editpanel:mount', this.crud, item[this.crud.id], this.saveItem);

                }

            },
            emitChanges(items){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", items);
            },
            pickItems(){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }


                if (this.crud)
                {

                    let crudEditor = new CrudBuilder( this.crud, CrudTypes.PICK_MANY )
                        .onPick((values)=> {
                            this.mergedItems(values);
                        })
                        .onGetItem(()=>this.item)
                        .build();

                    crudEditor.show();

                } else {
                    toastr.error("Не определена CRUD форма");
                }
            },
            mergedItems(pickedItems){

                if(pickedItems && pickedItems.length > 0){

                    _.each(pickedItems, (i)=>{
                        if (i.isSelected){
                            i.isSelected = false;
                        }

                        if (this.field.relation.pivot.length){

                            let sameItem = _.find(this.items, (ti)=>ti[this.crud.id] === i[this.crud.id]);

                            if (sameItem) {
                                this.$set(i, 'pivot', sameItem.pivot);
                            } else {
                                let emptyPivot = CrudUtils.createMetaObject(this.field.relation.pivot);
                                this.$set(i, 'pivot', emptyPivot);
                            }
                        }

                    });

                    if (this.items) pickedItems.splice(pickedItems.length, 0, ...this.items);

                    pickedItems = _.uniqBy(pickedItems, this.crud.id);

                    this.emitChanges(pickedItems);

                }
            }
        },
        beforeMount(){
            this.crud = AdminManager.getCrud(this.field.relation.crud.code);
        },
        mounted() {

        }
    }
</script>
