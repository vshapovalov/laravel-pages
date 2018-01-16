<template>
    <div v-if="active === true" :class="{modal: isPickMode, 'is-active': isPickMode}" style="z-index: 100000;">
        <div :class="{'modal-background': isPickMode}"></div>
        <div :class="{'modal-content' : isPickMode}" >

            <div :class="{'container': isPickMode, 'is-fluid': isPickMode}">
                    <div class="panel">
                            <div class="panel-heading">
                            <p class="title is-5">{{ title }}</p>
                        </div>
                        <div class="panel-block">
                            <div class="">
                                <a ref="upload" class="button is-success" :class="{'is-static': !isLibraryAvailable}">
                                    <div id="uploadPreview" style="display: none">

                                    </div>Загрузить</a>
                                <a :class="{'is-static': path.length<=1 || !isLibraryAvailable}"
                                   class="button is-warning" @click="goBack">Назад</a>
                                <a :class="{'is-static': !isLibraryAvailable}"
                                   class="button is-warning" @click="newFolder">Создать папку</a>
                                <!--<a :class="{'is-static': items.length == 0}" class="button is-warning" @click="moveItem">Переместить</a>-->
                                <a :class="{'is-static': !currentItem || (currentItem && currentItem.type != mediaTypes.FOLDER)} || !isLibraryAvailable"
                                   class="button is-warning" @click="renameItem">Переименовать</a>
                                <a :class="{'is-static': !currentItem || !isLibraryAvailable}"
                                   class="button is-warning" @click="deleteItem">Удалить</a>
                                <a v-if="isPickMode" :class="{'is-static': pickedItems.length === 0} || !isLibraryAvailable"
                                   class="button is-warning" @click="pickItem">Выбрать</a>
                                <a v-if="isPickMode" class="button is-danger" @click="closeLibrary">Закрыть</a>
                            </div>
                        </div>
                        <div ref="progressgroup" class="panel-block" style="display: none;">
                            <progress ref="progress" class="progress is-primary" :value="progressValue" max="100">{{progressValue}}%</progress>
                        </div>
                        <div class="panel-block">
                            <div class="breadcrumb" >
                                <ul>
                                    <li v-for="(pathItem, index) in path" @click="goToItem(pathItem)" :class="{'is-active': index == (path.length - 1)}">
                                        <a>{{ pathItem.title }}</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="panel-block is-block">
                            <div class="columns">
                                <div class="column is-three-quarters">
                                    <table class="table is-fullwidth" >
                                        <tr>
                                            <th data-role="button" @click="selectAll">*</th>
                                            <th>Название</th>
                                            <th>Добавлен</th>
                                        </tr>
                                        <tr v-for="item in items" @dblclick.stop="onOpenItem(item)" @click.stop="onSelectItem(item)"
                                            :class="[{'is-selected': currentItem && item.id === currentItem.id},{ 'notification is-success': isPickMode && item.isSelected}]">
                                            <td>
                                                <p v-if="isImage(item)" class="image is-16x16">
                                                    <img :src="fullPath(item)" alt="">
                                                </p>
                                            </td>
                                            <td><span class="is-unselectable">{{ item.title }}</span></td>
                                            <td><span class="is-unselectable">{{ item.created_at }}</span></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="column">
                                    <div v-if="currentItem">
                                        <p class="title is-6">{{ currentItem.title }}</p>
                                        <p class="subtitle is-6">{{ currentItem.created_at }}</p>
                                        <div v-if="currentItem.type === mediaTypes.ITEM">
                                            <div>
                                                <a :href="fullPath(currentItem)">Ссылка</a>
                                            </div>
                                            <figure class="image" v-if="isCurrentItemImage">
                                                <img :src="fullPath(currentItem)" alt="">
                                            </figure>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>


                    <div v-if="editForm.state != editStates.NONE" class="modal is-active">
                        <div class="modal-background"></div>
                        <div class="modal-card">
                            <header class="modal-card-head">
                                <p class="modal-card-title">{{ editForm.title }}</p>
                            </header>
                            <section class="modal-card-body">
                                <div class="edit-form">
                                    <div class="field ">
                                        <label class="label">Название</label>
                                        <div class="control">
                                            <input type="text" class="input" v-model="editForm.value">
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <footer class="modal-card-foot">
                                <a :class="{'is-static': !editForm.value, 'is-loading': editForm.state === editStates.POSTING }" class="button is-success" @click="submitEditForm">Подтвердить</a>
                                <a class="button is-danger is-outlined" @click="cancelEdit">Отмена</a>
                            </footer>
                        </div>
                    </div>

                    <div v-if="modalApprove.state != editStates.NONE" class="modal is-active">
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
    </div>
</template>

<script>

    // TODO: del, move actions
    // TODO: refactor simple modal to component

    import MediaTypes from './../utils/types';
    import MediaUtils from './../utils';
    import FormBehaviorTypes from './../../utils/types';
    import CrudApi from './../../api';
    import CrudUrls from './../../api/urls';


    let EditStates = {
        NONE: '',
        NEW_FOLDER: 'newfolder',
        RENAME_FOLDER: 'renamefolder',
        POSTING: 'posting'
    };

    let LibraryStates = {
        BROWSE: 'browse',
        LOADING: 'loading'
    };

    export default {
        name: 'media-library',
        props: {
            active: {
                default: true
            },
            type:{
                type: String
            },
            crudField:{
                type: Object,
                default: null
            }
        },
        data: function () {
            return {
                items: [],
                root: { title: 'Библиотека', id: 'root', path: '/storage/uploads/', type: 'folder'},
                currentFolder: undefined,
                currentItem: undefined,
                path: [ ],
                editForm: {
                    state: EditStates.NONE,
                    title: '',
                    value: ''
                },
                libraryState: LibraryStates.BROWSE,
                dropZone: {},
                progressValue: 0,
                modalApprove: {
                    title: '',
                    text: '',
                    state: EditStates.NONE,
                    onApprove: undefined
                },
                baseUrl: '',
                title: 'Медиа библиотека'
            }
        },
        computed: {
            mediaTypes(){
                return MediaTypes;
            },
            editStates(){
                return EditStates;
            },
            libraryStates(){
                return LibraryStates;
            },
            isLibraryAvailable(){
                return this.libraryState === LibraryStates.BROWSE;
            },
            formBehaviorTypes(){
                return FormBehaviorTypes;
            },
            isPickMode(){
                return this.type !== FormBehaviorTypes.BROWSE;
            },
            pickedItems(){
                return _.filter(this.items, (i) => i.isSelected);
            }


        },
        methods: {

            /******************************** behavior *********************************/

            pickItem(){
                let selectedItems = _.map(_.filter(this.items, (i) => i.isSelected), (i)=>this.storagePath(i));
                this.$emit("pick", selectedItems);
            },

            closeLibrary(){
                this.$emit("cancel");
            },

            /******************************** end behavior *********************************/

            /******************************** modal **********************************/

            showModal(title, text, onApprove){
                this.modalApprove.title = title;
                this.modalApprove.text = text;
                this.modalApprove.onApprove = onApprove;
                this.modalApprove.state = EditStates.POSTING;
            },

            modalApproveOk(){
                if (this.modalApprove.onApprove) {
                    this.modalApprove.onApprove();
                }

                this.modalApproveCancel();
            },

            modalApproveCancel(){
                this.modalApprove.onApprove = undefined;
                this.modalApprove.state = EditStates.NONE;
            },

            /******************************** end modal **********************************/



            currentItemFullPath(){
                return this.fullPath(this.currentItem);
            },
            storagePath(item){
                return uploadPath + '/' + item.id + '-' + item.title;
            },
            fullPath(item){
                return baseUrl + this.storagePath(item);
            },
            isImage(item){
                return MediaUtils.isImage(item.title);
            },
            isCurrentItemImage(){
                return this.isImage(this.currentItem.title);
            },

            /******************************** edit actions **********************************/

            selectAll(){
                _.forEach(this.items, (item)=>{ this.onSelectItem(item)});
            },
            newFolder(){
                if (!this.isLibraryAvailable)
                    return;

                this.editForm.title = "Создать папку";
                this.editForm.state = EditStates.NEW_FOLDER;
            },
            renameItem(){
                if (!this.isLibraryAvailable)
                    return;

                if (this.currentItem && this.currentItem.type === MediaTypes.FOLDER){
                    this.editForm.title = "Переименовать папку";
                    this.editForm.value = this.currentItem.title;
                    this.editForm.state = EditStates.RENAME_FOLDER;
                }
            },
            submitEditForm(){
                if ( _.trim(this.editForm.value)){
                    if (this.editForm.state === EditStates.NEW_FOLDER){

                        CrudApi.mediaFolderNew({
                            root: this.currentFolder,
                            title: this.editForm.value
                        }).then(()=>{
                            this.cancelEdit();
                            this.getItems();

                        })
                        .catch((error)=>{
                            this.cancelEdit();
                            console.log(error);

                            alert('При выполнении операции возникла ошибка.');
                        });
                    }

                    if (this.editForm.state === EditStates.RENAME_FOLDER){

                        CrudApi.mediaFolderRename({
                                item: this.currentItem,
                                title: this.editForm.value
                            })
                            .then(()=>{
                                this.cancelEdit();
                                this.getItems();
                            })
                            .catch((error)=>{
                                console.log(error);
                                this.cancelEdit();
                                alert('При выполнении операции возникла ошибка.');
                            });
                    }
                }
            },

            deleteItem(){
                if (!this.isLibraryAvailable)
                    return;

                if (this.currentItem){

                    this.showModal('Удаление', 'Подтвердите удаление ' + this.currentItem.title, ()=>{
                        CrudApi.mediaItemsDelete({
                            item: this.currentItem,
                            media_settings: this.crudField && this.crudField.additional ? this.crudField.additional : {}
                        })
                        .then(()=>{
                            this.getItems();
                            toastr.success('Успешно удалено');
                        }).catch((error)=>{
                            toastr.error(error);
                        });
                    });
                }
            },
            cancelEdit(){
                this.editForm.title = "";
                this.editForm.value = "";
                this.editForm.state = EditStates.NONE;
            },
            /******************************** end edit form **********************************/

            /******************************** common library actions *********************************/
            onOpenItem(item){
                if (item.type === MediaTypes.FOLDER){
                    this.path.splice(this.path.length, 0, item);
                    this.getItems();
                }
            },
            onSelectItem(item){
                if (!this.isLibraryAvailable)
                    return;

                if (this.isPickMode && (item.type === MediaTypes.ITEM)){

                    if (this.type === FormBehaviorTypes.PICK){
                        _.map(this.items, (i)=> {
                            if (i.id !== item.id)
                                this.$set(i, 'isSelected', false);
                        });
                    }

                    this.$set(item, 'isSelected', !item.isSelected);
                }

                this.currentItem = item;
            },

            goBack(){
                if (!this.isLibraryAvailable)
                    return;

                if (this.path.length > 1){
                    this.path.splice(this.path.length - 1, 1);
                    this.getItems();
                }
            },

            goToItem(pathItem){

                if (!this.isLibraryAvailable)
                    return;

                let itemIndex = _.findIndex(this.path, (p)=>p.id === pathItem.id);

                if (this.path.length > 0){
                    this.path.splice(itemIndex + 1);
                    this.getItems();
                }
            },
            /******************************** end common  library actions *********************************/


            getItems(){
                this.currentFolder = _.last(this.path);
                this.libraryState = LibraryStates.LOADING;
                CrudApi.mediaGetItems(this.currentFolder.id, this.path)
                    .then((response)=>{
                        this.libraryState = LibraryStates.BROWSE;
                        this.items = response.data;
                        this.currentItem = undefined;
                        mediaPath = this.path;
                    })
                    .catch((error)=>{
                        this.libraryState = LibraryStates.BROWSE;
                        console.log(error);
                    });
            }
        },
        beforeMount(){

            if (this.crudField) this.title = 'Редактирование - ' + this.crudField.caption;

            if (mediaPath && _.isArray(mediaPath) && mediaPath.length){
                this.path.splice(0,0, ...mediaPath);
                this.getItems();
            } else {
                this.onOpenItem(this.root);
            }
        },
        mounted() {

            $(this.$refs.upload).dropzone({
                url: CrudUrls.media.UPLOAD,
                previewsContainer: "#uploadPreview",
                totaluploadprogress: (uploadProgress, totalBytes, totalBytesSent)=>{
                    this.progressValue = uploadProgress;
                    if(uploadProgress === 100){
                        $(this.$refs.progressgroup).delay(1500).slideUp();
                    }
                },
                processing: ()=>{
                    this.progressValue = 0;
                    $(this.$refs.progressgroup).fadeIn();
                },
                sending: (file, xhr, formData) => {
                    formData.append("_token", CSRF_TOKEN);
                    formData.append("root_id", this.currentFolder.id);
                    if (this.crudField && this.crudField.additional &&
                        (this.crudField.additional.resize || this.crudField.additional.thumbnails))
                        formData.append("media_settings", JSON.stringify(this.crudField.additional));
                },
                success: function(e, res){
                    if(res.success){
                        toastr.success(res.message, "Загружено");
                    } else {
                        toastr.error(res.message, "Ошибочка");
                    }
                },
                error: function(e, res, xhr){
                    toastr.error(res, "Ошибочка");
                },
                queuecomplete: ()=>{
                    this.getItems();
                }
            });

        }
    }
</script>

<style>
    .modal-content {
        width: 100%;
        height: 100%;
        background-color: #fff;
        margin: 20px;
        padding: 20px;
        border-radius: 5px;
    }
</style>
