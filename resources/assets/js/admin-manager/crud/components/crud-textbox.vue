<template>
    <input ref="input" class="input" :type="inputType" :value="value" @input="onChange" />
</template>

<script>

    export default {
        name: "crud-textbox",
        model:{
            prop: "value",
            event: "change"
        },
        props: ['field', 'value'],
        data: function () {
            return {}
        },
        computed: {
            inputType(){
                if (this.field.additional && this.field.additional.mode === 'password') {

                    return 'password';
                } else {

                    return 'text';
                }
            },
        },
        methods: {
            onChange($event){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", $event.target.value);
            }
        },
        mounted() {
            if (this.field.additional) {

                if (this.field.additional.mode === 'password') {
                    this.$emit("change", "");
                } else {
                    if (this.field.additional.mode === 'masked' && this.field.additional.mask) {

                        $(this.$refs.input).mask(this.field.additional.mask, {clearIfNotMatch: true});
                    }
                }

            }


        }
    }
</script>
