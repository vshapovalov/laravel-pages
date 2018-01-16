import Library from './library';
import Utils from './utils';

export default class MediaLibraryBuilder{
    constructor(type){
        this.type = type;
        this.workspaceSelector = AdminManager.getWorkspaceSelector();
        this.crudField = null;
    }

    onPick(pickCallback){
        this.pickCallback = pickCallback;
        return this;
    }

    setCrudField(crudField){
        this.crudField = crudField;
        return this;
    }

    onCancel(cancelCallback){
        this.cancelCallback = cancelCallback;
        return this;
    }

    setWorkspaceSelector(workspaceSelector){
        this.workspaceSelector = workspaceSelector;
        return this;
    }

    build(){

        let elemetId = 'medialibrary' + Utils.randomInteger(1, 10000);

        $(this.workspaceSelector).append(`<div id="${elemetId}"></div>`);

        return new Library(elemetId, this.type, this.pickCallback, this.cancelCallback, this.crudField);
    }

}
