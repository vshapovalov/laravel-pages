import RowWrapper from './row-wrapper';
import FieldTypes from './field-types';
import RelationTypes from './relation-types';

export default class Utils {
    static randomInteger(min, max) {
        let rand = min - 0.5 + Math.random() * (max - min + 1);

        rand = Math.round(rand);

        return rand;
    }

    static spreadJsonFields(item, fields, spreadRelations = false){

        let jsonFields = _.filter(fields, (f)=>f.json);

        _.each(jsonFields, (f)=>{

            let jPath = f.name.split('->');

            let tmpValue = item[jPath[0]] ? JSON.parse(item[jPath[0]]) : null;

            jPath.splice(0,1);

            jPath = jPath.join('.');

            item[f.name] = _.get(tmpValue, jPath, this.defaultFieldValue(f));

        });

        if (spreadRelations) {

            _.each(fields, (f)=>{

                if ((f.type === FieldTypes.RELATION) &&
                    (f.relation.type === RelationTypes.HAS_MANY || f.relation.type === RelationTypes.BELONGS_TO_MANY ) ){

                    let relationCrud = AdminManager.getCrud(f.relation.crud.code);

                    _.each(item[ _.snakeCase(f.name) ], (relatedItem)=>{
                        this.spreadJsonFields(relatedItem, relationCrud.fields, false);
                    });
                }
            });

            let jsonPivotFields = _.filter(fields, (f)=>{
                return f.type === 'relation' && f.relation.type === 'belongsToMany'
                    && f.relation.pivot && _.some(f.relation.pivot, (f)=>f.json);
            });

            _.each(jsonPivotFields, (f)=>{

                let jsonFields = _.map(_.filter(f.relation.pivot, (f)=> f.json), (mf)=>{
                    let jPath = mf.name.split('->');
                    let rootFieldName = jPath[0];

                    jPath.splice(0,1);

                    jPath = jPath.join('.');

                    return {
                      rootFieldName,
                      name: mf.name,
                      jPath
                    };
                });

                _.each(item[_.snakeCase(f.name)], (pivotItem)=>{

                    _.each(jsonFields, (jf)=>{
                        let tmpValue = pivotItem.pivot[jf.rootFieldName] ? JSON.parse(pivotItem.pivot[jf.rootFieldName]) : null;
                        pivotItem.pivot[jf.name] = _.get(tmpValue, jf.jPath, this.defaultFieldValue(jf));
                    });

                });

            });
        }

        return item;
    }

    static defaultFieldValue(field){

        if (field.hasOwnProperty('by_default')){
            return field.by_default;
        }

        if (field.type === FieldTypes.TEXTBOX ||
            field.type === FieldTypes.CHECKBOX ||
            field.type === FieldTypes.COLORBOX ||
            field.type === FieldTypes.TEXTAREA ||
            field.type === FieldTypes.DROPDOWN ||
            field.type === FieldTypes.DATEPICKER ||
            field.type === FieldTypes.RICHEDIT ||
            field.type === FieldTypes.TEXTBOX ||
            field.type === FieldTypes.IMAGE ){
            return "";
        }

        if (field.type === FieldTypes.RELATION){
            if (field.relation.type === RelationTypes.BELONGS_TO
                || field.relation.type === RelationTypes.HAS_ONE){
                return {};
            }
            if (field.relation.type === RelationTypes.HAS_MANY
                || field.relation.type === RelationTypes.BELONGS_TO_MANY){
                return [];
            }
        }
    }

    static createMetaObject(fields){
        let item = {};

        _.each(fields, (f)=>{
            item[ f.json ? f.name : _.snakeCase(f.name) ] = this.defaultFieldValue(f);
        });

        return item;
    }

    static wrapItems(items){
        let wrappedRows = [];

        if (items.length){
            wrappedRows = _.map(items, (i)=> this.wrapItem(i));
        }

        return wrappedRows;
    }

    static wrapItem(item){
        return new RowWrapper(item);
    }

    static unwrapItems(wrappedRows){
        let unwrappedRows = [];

        if (wrappedRows.length){
            unwrappedRows = _.map(wrappedRows, (r)=> this.unwrapItem(r));
        }

        return unwrappedRows;
    }

    static unwrapItem(wrappedRow){
        return wrappedRow.item;
    }


    static getItemsMaxOrderByLevel(level, id, items){

        let maxOrder = 0;

        _.forEach(items, (i)=>{
            if (i.level === level && (i.id !== id) && (i.order > maxOrder)) {
                maxOrder = i.order;
            }
        });

        return maxOrder;

    }

    static getPrevTreeSiblingById(id, siblings){
        let sibling = undefined;

        siblings.forEach((s, i, arr)=>{
            if ((s.id === id) && ((i-1) >= 0)) {
                sibling = arr[i-1];
            }
        });

        return sibling;
    }

    static getNextTreeSiblingById(id, siblings){
        let sibling = undefined;

        siblings.forEach((s, i, arr)=>{
            if ((s.id === id) && ((i+1) <= arr.length-1)) {
                sibling = arr[i+1];
            }
        });

        return sibling;
    }

    static getDisplayValue(fieldName, item){
        return item && (fieldName.indexOf('.') >= 0 ) ? _.get(item, fieldName, '') : item[fieldName];
    }

}

