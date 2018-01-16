<template>
    <div v-if="active === true" :class="{modal: isPickMode, 'is-active': isPickMode}">
        <div :class="{'modal-background': isPickMode}"></div>
        <div :class="{'modal-content' : isPickMode}" >
            <!--style="width: 100%; height: 100%;background-color: #fff; margin: 20px; padding: 20px; border-radius: 5px;"-->
            <div :class="{'container': isPickMode, 'is-fluid': isPickMode}">
                <div class="panel">
                    <p class="panel-heading">
                        {{ crud.name }}
                    </p>
                    <div class="panel-block">
                        <div>
                            <a href="#" class="button is-primary is-outlined" @click.prevent="getList">Обновить</a>
                            <a href="#" class="button is-success" @click="addItem">Добавить</a>

                            <a v-if="isPickMode" href="#" class="button is-primary" @click="pickRows">Выбрать</a>
                            <a v-if="isPickMode" href="#" class="button is-danger is-outlined" @click="pickCancel">Отмена</a>
                        </div>
                    </div>
                    <div class="panel-block" :class="{'is-blured': !isReady}">
                        <crud-table v-if="crud.type === 'list'" class="is-fullwidth" :items="items"
                                    :crud="crud" :type="editorType" @edit="editItem" @delete="deleteItem"
                                    @select="selectItems" @sort="onSortItems"></crud-table>
                        <crud-tree v-else-if="crud.type === 'tree'" :items="items" :crud="crud" :type="editorType"
                                   @edit="editItem" @delete="deleteItem" @select="selectItems"
                                   @change="onTreeChange"></crud-tree>
                    </div>
                </div>
            </div>

            <div v-if="modalApprove.state != editorTypes.BROWSE" class="modal is-active">
                <div class="modal-background"></div>
                <div class="modal-card">
                    <header class="modal-card-head">
                        <p class="modal-card-title">{{ modalApprove.title }}</p>
                    </header>
                    <section class="modal-card-body">
                        <p class="modal-card-title">{{ modalApprove.text }}</p>
                    </section>
                    <footer class="modal-card-foot">
                        <a class="button is-success" @click="modalApproveOk">Подтвердить</a>
                        <a class="button is-danger is-outlined" @click="modalApproveCancel">Отмена</a>
                    </footer>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    import States from './../utils/states';
    import CrudTypes from './../../utils/types';
    import CrudUtils from './../utils';
    import CrudApi from './../../api';
    import RowWrapper from './../utils/row-wrapper';

    // TODO: refactor modal form to component

    export default {
        name: 'crud-editor',
        props: {
            crud: {
                type: Object
            },
            editorType: {
                type: String
            },
            active: {
                default: true
            },
            item: {}
        },
        data: function () {
            return {
                items: [],
                editingRow: {},
                state: States.BROWSE,
                baseUrl: '',
                isReady: false,
                selectedItems: [],
                modalApprove: {
                    title: '',
                    text: '',
                    state: CrudTypes.BROWSE,
                    onApprove: undefined
                },
                sortField: '',
                sortType: 'asc'
            }
        },

        computed: {
            editorTypes(){ return CrudTypes; },
            isPickMode(){ return this.editorType !== CrudTypes.BROWSE; },
        },
        methods: {

            /******************************** modal **********************************/

            showModal(title, text, onApprove){
                this.modalApprove.title = title;
                this.modalApprove.text = text;
                this.modalApprove.onApprove = onApprove;
                this.modalApprove.state = CrudTypes.ADD;
            },

            modalApproveOk(){
                if (this.modalApprove.onApprove) {
                    this.modalApprove.onApprove();
                }

                this.modalApproveCancel();
            },

            modalApproveCancel(){
                this.modalApprove.onApprove = undefined;
                this.modalApprove.state = CrudTypes.BROWSE;
            },

            /******************************** end modal **********************************/

            /****************************** PICKER *************************************/

            pickRows(){
                this.$emit('pick', this.selectedItems);
            },

            pickCancel(){
                this.$emit('cancel');
            },

            /****************************** END PICKER *************************************/

            onTreeChange(items){

                CrudApi.crudTreeMove(this.crud.code, { data: items })
                    .then((response)=>{
                        toastr.success('Положение обновлено');
                    })
                    .catch((error)=>{
                        toastr.error(error, 'Не удалось обновить положение');
                    });
            },

            /****************************** EDITOR *************************************/
            addItem(){
                Bus.$emit('editpanel:mount', this.crud, null, this.saveItem);
            },
            editItem(item){
                Bus.$emit('editpanel:mount', this.crud, item[this.crud.id], this.saveItem);
            },

            saveItem(item){
                console.log('this.editingRow', item);

                this.getList();

            },
            deleteItem(item){

                this.showModal("Удалить запись?","Подтвердите удаление записи",()=>{
                    this.isReady = false;
                    CrudApi.crudDeleteItem(this.crud.code, item[this.crud.id])
                        .then((response)=>{
                            let rowIndex = _.findIndex(this.items, (r)=>{ return (r[this.crud.id] === item[this.crud.id]) });

                            this.items.splice(rowIndex, 1);
                            this.isReady = true;
                            toastr.success('Запись удалена');
                        })
                        .catch((error)=>{
                            this.isReady = true;
                            toastr.error(error, 'Ошибка удаления записи');
                            console.log(error);
                        });
                });
            },

            /****************************** END EDITOR *************************************/

            selectItems(items){
                this.selectedItems = items;
            },

            onSortItems(fieldName){
                if (this.sortField === fieldName) {
                    if (this.sortType === 'asc'){
                        this.sortType = 'desc';
                    }
                    else {
                        this.sortType = 'asc';
                    }
                } else {
                    this.sortType = 'asc'
                }

                this.sortField = fieldName;

                this.getList();
            },

            getList(){
                this.isReady = false;
                CrudApi.crudGetItems(this.crud.code, { item: this.item, sort: { field: this.sortField, type: this.sortType }})
                    .then((response)=>{

                        this.items = response.data.items.map((i)=>{
                            return CrudUtils.spreadJsonFields(i, this.crud.fields, false);
                        });
                        this.isReady = true;
                    })
                    .catch((error)=>{
                        this.isReady = true;

                        toastr.error(error, 'Не удалось получить список');
                    });
            }
        },
        mounted() {
            this.getList();
        }
    }
</script>

<style >
    .modal-content {
        width: 100%;
        height: 100%;
        background-color: #fff;
        margin: 20px;
        padding: 20px;
        border-radius: 5px;
    }
</style>

<style lang="scss">
    .is-blured{
        filter: blur(2px);
    }
</style>