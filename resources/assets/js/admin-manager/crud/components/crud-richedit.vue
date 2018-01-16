<template>
    <textarea id="richedit" class="textarea" ref="tiny" :value="value" @change="onChange"></textarea>
</template>

<script>
    let tinymce = require('tinymce');

    import FormBehaviorTypes from '../../utils/types';
    import LibraryBuilder from './../../media/builder';

    // TODO: add custom skin for tinymce
    export default {
        name: 'crud-richedit',
        model:{
            prop: "value",
            event: "change"
        },
        props: ["value", "field"],
        data: function () {
            return {}
        },
        computed: {},
        methods: {
            onChange(content){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", content);
            }
        },
        beforeDestroy(){
            tinymce.remove();
        },
        mounted() {
            tinymce.init({
                content_css: '/css/vendors/tinymce.css',
                menubar: false,
                target: this.$refs.tiny,
                min_height: 600,
                resize: 'vertical',
                plugins: 'link, lists, image, code, youtube, giphy, table, textcolor, fullscreen, advlist, colorpicker, contextmenu',
                extended_valid_elements : 'input[id|name|value|type|class|style|required|placeholder|autocomplete|onclick]',
                file_browser_callback: function(field_name, url, type, win) {

                    if(type =='image'){
                        $('#upload_file').trigger('click');
                    }
                },
                toolbar: 'styleselect bold italic underline | forecolor backcolor | alignleft aligncenter alignright | bullist numlist outdent indent | link image table youtube giphy | code',
                convert_urls: false,
                image_caption: true,
                image_title: true,
                file_picker_callback: function(callback, value, meta) {
                    if (meta.filetype == 'image' || meta.filetype == 'file') {
                        new LibraryBuilder(FormBehaviorTypes.PICK).onPick((items)=>{
                            callback(_.first(items), {});
                        }).build().show();
                    }

                    // Provide alternative source and posted for the media dialog
//                    if (meta.filetype == 'media') {
//                        callback('movie.mp4', {source2: 'alt.ogg', poster: 'image.jpg'});
//                    }
                },
                setup:(ed)=>{
                    ed.on('change', (e)=>{
                        this.onChange(ed.getContent());
                    });

//                    ed.on('init', (e) =>{
//                        ed.execCommand("fontName", false, "Arial");
//                        ed.execCommand("fontSize", false, "2");
//                    });

                }
            });
        }
    }
</script>
