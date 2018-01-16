<template>
    <div v-if="active" class="modal is-active">
        <div class="modal-background"></div>
        <div class="modal-card" style="height: 100%; width: 100%; margin: 20px;" :class="{'is-blured': !isReady}">
            <header class="modal-card-head">
                <p class="modal-card-title">{{ editState === editStates.ADD ? 'Создание' : 'Редактирование' }} [{{ crud.name }}]</p>
                <button class="delete" aria-label="close" @click="doCancel"></button>
            </header>
            <div class="tabs">
                <ul>
                    <li v-for="tab in crudTabs" :class="{'is-active' : activeTab == tab}"><a @click="setActiveTab(tab)">{{ tab }}</a></li>
                </ul>
            </div>
            <section class="modal-card-body">
                <div class="edit-form">


                    <div class="flex-tab" v-for="tab in crudTabs" v-show="activeTab == tab">
                        <div class="field " :class="[getFieldWidth(field)]" v-for="field in crudEditableFields" v-if="field.tab == tab || (!field.tab & tab == 'Основные параметры')">
                            <label class="label">{{ field.caption }}<small v-show="field.description"> ({{field.description }})</small></label>

                            <div class="control">

                                <crud-textbox v-if="getFieldType(field) === fieldTypes.TEXTBOX" v-model="item[field.name]" :field="field" @change="onSlugify(field)"></crud-textbox>
                                <crud-checkbox v-if="getFieldType(field) === fieldTypes.CHECKBOX" v-model="item[field.name]" :field="field"></crud-checkbox>
                                <crud-textarea v-if="getFieldType(field) === fieldTypes.TEXTAREA" v-model="item[field.name]" :field="field"></crud-textarea>
                                <crud-richedit v-if="getFieldType(field) === fieldTypes.RICHEDIT" v-model="item[field.name]" :field="field"></crud-richedit>
                                <crud-datepicker v-if="getFieldType(field) === fieldTypes.DATEPICKER" v-model="item[field.name]" :field="field"></crud-datepicker>
                                <crud-colorbox v-if="getFieldType(field) === fieldTypes.COLORBOX" v-model="item[field.name]" :field="field"></crud-colorbox>
                                <crud-image v-if="getFieldType(field) === fieldTypes.IMAGE" v-model="item[field.name]" :field="field"></crud-image>
                                <crud-dropdown v-else-if="getFieldType(field) === fieldTypes.DROPDOWN" v-model="item[field.name]"
                                        :field="field"></crud-dropdown>
                                <crud-relation-one
                                        v-else-if="(getFieldType(field) === fieldTypes.RELATION && (field.relation.type === relationTypes.BELONGS_TO || field.relation.type === relationTypes.HAS_ONE))"
                                        v-model="item[field.json ? field.name : toSnake(field.name)]" :field="field"></crud-relation-one>
                                <crud-relation-many
                                        v-else-if="(getFieldType(field) === fieldTypes.RELATION && field.relation.type == relationTypes.HAS_MANY)"
                                        v-model="item[toSnake(field.name)]" :field="field" :item="item"></crud-relation-many>
                                <crud-relation-many
                                        v-else-if="(getFieldType(field) === fieldTypes.RELATION && field.relation.type == relationTypes.BELONGS_TO_MANY)"
                                        v-model="item[toSnake(field.name)]" :field="field" :item="item"></crud-relation-many>
                            </div>
                        </div>
                    </div>
                    <div class="field "></div>
                </div>
            </section>
            <footer class="modal-card-foot">
                <a href="#" class="button is-success" @click.prevent.stop="doSave">Сохранить</a>
                <a href="#" class="button is-danger is-outlined" @click.prevent.stop="doCancel">Отмена</a>
            </footer>
        </div>
    </div>
</template>

<script>
    import RelationTypes from './../utils/relation-types';
    import FieldTypes from './../utils/field-types';
    import VisibilityTypes from './../utils/visibility-types';
    import EditStates from './../utils/states';
    import CrudUtils from './../utils';
    import CrudApi from './../../api';

    export default {
        name: 'crud-editpanel',
        props: ['item-id', 'crud', 'active'],
        data: function () {
            return {
                activeTab: '',
                item: {},
                isReady: false
            }
        },
        computed: {
            editState(){
                return this.itemId ? EditStates.EDIT : EditStates.ADD;
            },
            editStates(){
                return EditStates;
            },
            crudEditableFields(){

                let visibilityFilter;

                switch (this.editState){
                    case EditStates.ADD:{
                        visibilityFilter = VisibilityTypes.ADD;
                        break;
                    }
                    case EditStates.EDIT:{
                        visibilityFilter = VisibilityTypes.EDIT;
                        break;
                    }
                    default:{
                        visibilityFilter = VisibilityTypes.BROWSE;
                    }
                }

                return _.filter( this.crud.fields, (f)=> f.visibility.indexOf(visibilityFilter) >= 0 );

            },
            crudTabs(){
                return _.keys(_.groupBy(this.crud.fields, (f)=> f.tab ? f.tab : "Основные параметры") );
            },
            fieldTypes(){ return FieldTypes; },
            relationTypes(){ return RelationTypes; },
        },

        methods: {
            getFieldWidth(field){
                return 'is-' + field.columns;
            },

            onSlugify(field){
                if(field.additional && field.additional.slugify){
                    this.item[field.additional.slugify] = slugify(this.item[field.name]).toLowerCase();
                }
            },

            getFieldValue(field){
                let value = this.item[field.name];

                if (field.json) {
                    let jPath = field.name.split('->');

                    let tmpValue = this.item[jPath[0]];

                    jPath.splice(0,1);

                    _.each(jPath,(p)=>{
                        tmpValue = tmpValue[p];
                    });

                    value = tmpValue;
                }

                return value;
            },
            getFieldType(field){
                let result = '';

                if (field.type === FieldTypes.DYNAMIC){

                    if (field.additional && field.additional.type === 'related') {

                        let [fieldName, relationField] = field.additional.from.split('.');

                        result = this.item[_.snakeCase(fieldName)][relationField];

                    } else {

                        if (field.additional && field.additional.type === 'field') {

                            result = this.item[field.additional.from];
                        }
                    }
                } else {
                    result = field.type;
                }

                return result;
            },

            toSnake(val){
                return _.snakeCase(val);
            },
            /****************************** tabs *************************************/

            setActiveTab(tabName){
                this.activeTab = tabName;
            },

            /****************************** end tabs *************************************/

            doSave(){
                this.isReady = false;

                console.log('this.item', this.item);

                CrudApi.crudSaveItem(this.crud.code, { item: this.item})
                    .then((response)=>{

                        if (response.data.status === 'success'){

                            this.$emit('save', response.data.item);
                            toastr.success('Запись обновлена');
                        } else {

                            if (response.data.status === 'error'){

                                if (response.data.errors) {
                                    _.forEach(response.data.errors, (f)=>{
                                        _.forEach(f, (m)=>{
                                            toastr.error(m);
                                        });
                                    });

                                } else {
                                    toastr.error('Проверьте заполненность полей', 'Ошибка сохранения записи');
                                }
                            }
                        }
                        this.isReady = true;
                    })
                    .catch((error)=>{
                        toastr.error(error, 'Ошибка сохранения записи');
                        console.log(error);
                        this.isReady = true;
                    });
            },
            doCancel(){
                this.$emit('cancel');
            },
        },
        beforeMount() {
            this.activeTab = this.crudTabs[0];
            if (!this.itemId) {
                this.item = CrudUtils.createMetaObject(this.crud.fields);
                console.log('editpanel:newitem', this.item);
                CrudUtils.spreadJsonFields(this.item, this.crud.fields, true);
                this.isReady = true;
            } else {
                CrudApi.crudGetItem(this.crud.code, this.itemId)
                    .then((response)=>{
                        CrudUtils.spreadJsonFields(response.data, this.crud.fields, true);
                        this.item = response.data;
                        this.isReady = true;
                    })
                    .catch((error)=>{
                        toastr.error(error, 'Не удалось получить запись');
                        console.error('editpanel:getitem', error);
                        this.isReady = true;
                    });
            }
        }
    }
</script>

<style lang="scss" scoped>


    .is-blured{
        filter: blur(2px);
    }

    .tabs{
        background: white;
        margin: 0 !important;
        flex-shrink: 0;

        & > ul > li:first-child {
            margin-top: 0.25em;
        }
    }

    .field{
        padding-left: 5px;

        &.is-3{
            width: percentage(1/4);
        }

        &.is-4{
            width: percentage(1/3);
        }

        &.is-6{
            width: percentage(1/2);
        }

        &.is-12{
            width: percentage(1);
        }
    }

    .flex-tab{
        display: flex;
        flex-wrap: wrap;
    }
    .modal-card-body {
        padding: 0 20px 0 20px;
    }
</style>