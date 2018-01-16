<template>
    <span>
        <span v-if="fieldType === fieldTypes.TEXTBOX">{{ fieldValue }}</span>
        <span v-else-if="fieldType === fieldTypes.TEXTAREA">{{ fieldValue }}</span>
        <span v-else-if="fieldType === fieldTypes.DATEPICKER">{{ fieldValue }}</span>
        <span v-else-if="fieldType === fieldTypes.RICHEDIT">{{ fieldValue }}</span>
        <span v-else-if="fieldType === fieldTypes.COLORBOX" :style="{'background-color': fieldValue}" class="tag">{{ fieldValue }}</span>
        <span v-else-if="fieldType === fieldTypes.CHECKBOX ">{{ (fieldValue && (fieldValue !==0) && (fieldValue !== "0")) ? "Да" : "Нет" }}</span>
        <span v-else-if="fieldType === fieldTypes.DROPDOWN">{{ fieldValue }}</span>
        <span v-else-if="fieldType === fieldTypes.RELATION && (field.relation.type === relationTypes.BELONGS_TO || field.relation.type === relationTypes.HAS_ONE)">
            {{ !field.json ? ( item[field.name] ? getDisplayValue(getCrud(field.relation.crud.code)['display'], item[field.name]) : '') : item[field.name] }}
        </span>
        <span v-else-if="fieldType === fieldTypes.RELATION && field.relation.type === relationTypes.HAS_MANY">
            <span class="" v-for="(relationItem, index) in item[toSnakeCase(field.name)]">{{ (index > 0) ? ',' : '' }} {{ relationItem[getCrud(field.relation.crud.code)['display']] }}</span>
        </span>
        <span v-else-if="fieldType === fieldTypes.RELATION && field.relation.type === relationTypes.BELONGS_TO_MANY">
            <span class="" v-for="(relationItem, index) in item[toSnakeCase(field.name)]">{{ (index > 0) ? ',' : '' }} {{ relationItem[getCrud(field.relation.crud.code)['display']] }}</span>
        </span>
        <span v-else-if="fieldType === fieldTypes.IMAGE">
            <span v-if="field.additional && (field.additional && (field.additional.mode === 'single'))">
                <span class="image is-64x64">
                    <img :src="fieldValue" alt="">
                </span>
            </span>
            <span v-if="!field.additional || (field.additional.mode === 'multi')">
                <span v-for="image in getImages(fieldValue)" class="image is-48x48 is-inline-block">
                    <img :src="image" alt="">
                </span>
            </span>
        </span>
    </span>
</template>

<script>

    /*
    * pivot compatible with base type fields like [textbox, textarea, datepicker, colorbox, checkbox, image, richedit]
    *
    */

    import FieldTypes from './../utils/field-types';
    import RelationTypes from './../utils/relation-types';
    import Utils from './../utils';

    export default {
        name: 'crud-caption',
        props: {
            field: { type: Object},
            item: { }
        },
        data: function () {
            return {
                relatedCrud: null
            }
        },
        computed: {
            fieldType(){
                let result = '';

                if (this.field.type === FieldTypes.DYNAMIC){
                    if (this.field.additional && this.field.additional.type === 'related') {

                        let [fieldName, relationField] = this.field.additional.from.split('.');

                        result = this.item[_.snakeCase(fieldName)][relationField];

                    } else {

                        if (this.field.additional && this.field.additional.type === 'field') {

                            result = this.item[this.field.additional.from];
                        }
                    }

                } else {
                    result = this.field.type
                }

                return result;
            },
            fieldTypes(){
                return FieldTypes;
            },
            relationTypes(){
                return RelationTypes;
            },
            fieldValue(){

                let value = this.item[this.field.name];

                if (this.field.isPivot) {

                    value = this.item.pivot[this.field.name];
                }

                if (this.field.type === FieldTypes.DROPDOWN && this.field.additional) {

                    if (!this.field.additional.mode || this.field.additional.mode === 'single') {

                        let row = _.find(this.field.additional.values, (v)=>v.key === value);

                        if (row) {

                            value = row.value;
                        }
                    } else {

                        let valArr;

                        try {

                            valArr = JSON.parse(value);
                        } catch (e) {
                        }

                        if (valArr) {

                            value = _.map(_.filter(this.field.additional.values, (v) => valArr.indexOf(v.key) >= 0), (m) => m.value).join(', ');
                        }
                    }
                }

                return value;
            }
        },
        methods: {
            getDisplayValue(fieldName, item){
                return Utils.getDisplayValue(fieldName, item);
            },

            toSnakeCase(val){
                return _.snakeCase(val);
            },
            getImages(val){
                let parsedVal = [];
                try {
                    parsedVal = JSON.parse(val);
                } catch(e){
                }

                if (parsedVal)
                    return parsedVal;
                else
                    return [];

            },
            getCrud(crudCode){

                if (!this.relatedCrud){
                    this.relatedCrud = AdminManager.getCrud(crudCode);
                }

                return this.relatedCrud;
            }
        },
        mounted() {

        }
    }
</script>
