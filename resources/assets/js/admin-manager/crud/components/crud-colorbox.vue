<template>
    <input type="color" class="input" :default-value="value" :value="value" @change="onChange">
</template>

<script>
    export default {
        model: {
            prop: 'value',
            event: 'change'
        },
        name: 'crud-colorbox',
        props: ['field', 'value'],
        data: function () {
            return {
            }
        },
        computed: {},
        methods: {
            onChange(event){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", event.target.value);
            }
        },
        mounted() {

        }
    }
</script>
