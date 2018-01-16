
import AdminApi from './api';

export default class AdminManager{
    constructor(managerSelector, workspaceSelector){

        this.managerSelector = managerSelector;
        this.workspaceSelector = workspaceSelector ? workspaceSelector : '#workspace';
        this.cruds = {};
        let _this = this;
        this.managerInstance = new Vue({
            el: this.managerSelector,
            template: "<admin-manager ></admin-manager>",
        });

        this.requestConfig();
    }

    requestConfig(){

        AdminApi.getConfig()
            .then((resp)=>{
                _.each(resp.data.list, (item)=>{
                    this.cruds[item.code] = item;
                });
                this.managerInstance.$emit('config:loaded', resp.data);
            })
            .catch((error)=>{
                toastr.error(error, 'Не удалось получить конфигурацию!');
            });

    }

    getManagerInstance(){
        return this.managerInstance;
    }

    getWorkspaceSelector(){
        return this.workspaceSelector;
    }

    getCrud(crudCode){
        return this.cruds[crudCode];

    }

    mount(){

    }

}

