<template>
    <table class="table">
        <thead>
        <tr>
            <th data-role="button" v-for="field in crudBrowsableFields" @click.stop="sort(field)">{{ field.caption }}</th>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <tr v-show="items && items.length === 0">
            <td>Нет записей</td>
        </tr>
        <tr v-for="item in items" @click.prevent="selectItem(item)" :class="{'is-selected': item.isSelected}">
            <td v-for="field in crudBrowsableFields">
                <crud-caption :item="item" :field="field" :is-pivot="field.isPivot"></crud-caption>
            </td>
            <td>
                <a v-if="rowActions.indexOf('edit') >= 0" class="button is-warning is-outlined"
                   @click.prevent.stop="editItem(item)"><i class="fa fa-pencil"></i></a>
                <a v-if="rowActions.indexOf('delete') >= 0" class="button is-danger is-outlined"
                   @click.prevent.stop="deleteItem(item)"><i class="fa fa-trash"></i></a>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<script>
    import States from './../utils/states';
    import CrudTypes from './../../utils/types';
    import CrudUtils from './../utils';
    import RowWrapper from './../utils/row-wrapper';
    import FieldTypes from './../utils/field-types';
    import RelationTypes from './../utils/relation-types';
    import VisibilityTypes from './../utils/visibility-types';

    export default {
        name: 'crud-table',
        props: {
            crud: { type: Object },
            type: { type: String },
            items: { type: Array },
            rowActions: {
                type: Array,
                'default': ()=>['edit', 'delete']
            },
            pivotFields: { default : ()=>[]},
        },
        data: function () {
            return {

            }
        },
        computed: {
            crudBrowsableFields(){

                let fields = _.filter(
                    this.crud.fields, (f)=> (_.findIndex(f.visibility, (v) => v === VisibilityTypes.BROWSE ) >= 0)
                );

                if (this.pivotFields.length) {
                    fields.splice(fields.length, 0, ..._.map(_.filter(this.pivotFields, (pv)=>_.some(pv.visibility,(v)=>v===VisibilityTypes.BROWSE)),(f)=>{
                        f.isPivot = true;
                        return f;
                    }));
                }

                return fields;
            },
            editorTypes(){ return CrudTypes; },
            isPickMode(){ return this.type !== CrudTypes.BROWSE; },
            fieldTypes(){ return FieldTypes; },
            relationTypes(){ return RelationTypes; }
        },
        methods: {
            selectItem(item){

                if (this.isPickMode){

                    if (this.type === CrudTypes.PICK){
                        _.map(this.items, (i)=> {
                            if (i[this.crud.id] !== item[this.crud.id])
                                this.$set(i, 'isSelected', false);
                        });
                    }

                    this.$set(item, 'isSelected', !item.isSelected);

                    this.$emit("select", _.filter(this.items, (i) => i.isSelected ));

                }

            },

            editItem(item){

                this.$emit('edit', item);

            },

            deleteItem(item){

                this.$emit('delete', item);

            },
            sort(field){
                this.$emit('sort', field.name);
            }
        },
        mounted() {

        }
    }
</script>

