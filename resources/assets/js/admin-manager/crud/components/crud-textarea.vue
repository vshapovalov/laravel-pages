<template>
    <textarea class="textarea" :value="value" @change="onChange"></textarea>
</template>

<script>
    export default {
        name: 'crud-textarea',
        model:{
            prop: 'value',
            event: 'change'
        },
        props: ['field', 'value'],
        data: function () {
            return {}

        },
        computed: {},
        methods: {
            onChange(event){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", event.target.value)
            }
        },
        mounted() {

        }
    }
</script>
