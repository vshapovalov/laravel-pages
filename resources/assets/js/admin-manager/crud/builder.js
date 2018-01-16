import CrudUtils from './utils';
import CrudEditor from './editor';

export default class CrudBuilder{

    constructor(crud, crudType){
        this.crud = crud;
        this.crudType = crudType;
        this.workspaceSelector = AdminManager.getWorkspaceSelector();
    }

    onPick(callback){
        this._onPick = callback;
        return this;
    }

    onGetItem(callback){
        this._onGetItem = callback;
        return this;
    }

    onCancel(callback){
        this._onCancel = callback;
        return this;
    }

    setWorkspaceSelector(workspaceSelector){
        this.workspaceSelector = workspaceSelector;
        return this;
    }

    build(){

        let elemetId = 'crud' + CrudUtils.randomInteger(1, 10000);

        $(this.workspaceSelector).append(`<div id="${elemetId}"></div>`);

        return new CrudEditor(
            elemetId,
            this.crud,
            this.crudType,
            this._onPick,
            this._onCancel,
            this._onGetItem);
    }
}