<template>
    <label class="checkbox">
        <input type="checkbox" :checked="checked" @click="onClick">
    </label>
</template>

<script>

    // TODO: refactor to true checkbox component :)

    export default {
        name: "crud-checkbox",
        model:{
            prop: "value",
            event: "change"
        },
        props: ['field', 'value'],
        data: function () {
            return {
                checked: false
            }
        },
        computed: {},
        methods: {
            onClick($event){
                if (this.field.readonly)
                {
                    toastr.info("Редактирование запрещено");
                    return;
                }

                this.$emit("change", $event.target.checked ? 1 : 0);
            }
        },
        watch: {
            value: {
                handler: function(val, oldVal){

                    if (val && val !== 0 && val !== "0") {
                        this.checked = true
                    } else {
                        this.checked = false
                        this.$emit("change", 0);
                    }

                },
                immediate: true
            }
        },
        mounted() {

        }
    }
</script>
