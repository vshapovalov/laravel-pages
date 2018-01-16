import apiUrls from './urls';

export default class CrudApi {

    static getUrlByCrud(code){
        return apiUrls.crud.BASE + code;
    }

    static crudList(){

        return axios.get(apiUrls.crud.BASE + apiUrls.crud.LIST);
    }

    static getConfig(){

        return axios.get(apiUrls.crud.BASE + apiUrls.config);
    }

    static menuList(){

        return axios.get(apiUrls.crud.BASE + apiUrls.menu.LIST);
    }

    static crudTreeMove(code,data){

        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.TREE_MOVE, data);
    }

    static crudGetItem(code, id){

        return axios.get(this.getUrlByCrud(code) + '/' + id);
    }

    static crudGetItems(code, data){
        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.GET_ITEMS, data);
    }

    static crudSaveItem(code, data){

        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.SAVE_ITEM, data);
    }

    static crudDeleteItem(code, id){

        return axios.post(this.getUrlByCrud(code) + apiUrls.crud.DELETE_ITEM + id);
    }

    static mediaFolderNew(data){
        return axios.post(apiUrls.media.FOLDER_NEW, data);
    }

    static mediaFolderRename(data){
        return axios.post(apiUrls.media.FOLDER_RENAME, data);
    }

    static mediaItemsDelete(data){
        return axios.post(apiUrls.media.ITEMS_DELETE, data);
    }
    static mediaGetItems(folderId, path){
        return axios.post(apiUrls.media.GET_ITEMS, { root: folderId, path: path});
    }

}

