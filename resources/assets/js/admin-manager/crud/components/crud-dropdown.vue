<template>
    <div class="dropdown" :class="{'is-active': opened}" @click="toggleOpened">
        <div class="dropdown-trigger">
            <button class="button" aria-haspopup="true" aria-controls="dropdown-menu">
                <span>{{ selectedValue ? selectedValue : "выберите значение" }}</span>
                <span class="icon is-small" @click.prevent.stop="toggleOpened">
                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                </span>
            </button>
        </div>
        <div class="dropdown-menu" role="menu">
            <div class="dropdown-content">
                <table class="table" style="margin-bottom: 0;">
                    <!--<thead>-->
                        <!--<tr>-->
                            <!--<th>Значение</th>-->
                        <!--</tr>-->
                    <!--</thead>-->
                    <tbody>
                        <tr v-for="row in rows" @click.prevent.stop="selectItem(row)" :class="{ 'is-selected' : row.isSelected}">
                            <td>{{ row.value }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'crud-dropdown',
        model: {
            prop: "value",
            event: "change"
        },
        props: {
            value: {},
            field: {}
        },
        data: function () {
            return {
                selectedRow: { key: '', value : ''},
                opened: false,
                rows: []
            }
        },
        watch:{
            value(val, oldVal){
                if (oldVal === undefined) this.parseValue();
            }
        },
        computed: {
            isSingleMode(){

                return !(this.field.additional && this.field.additional.mode && (this.field.additional.mode === 'multi'));
            },
            selectedValue(){

                if (!this.isSingleMode){

                    return _.map(_.filter(this.rows, (r)=>r.isSelected),(z)=>z.value).join(', ');
                } else {

                    return this.selectedRow ? this.selectedRow.value : undefined;
                }
            }
        },
        methods: {
            toggleRowSelected(row){
                this.$set(row, 'isSelected', !row.isSelected)
            },
            emitChanges(){

                if (this.isSingleMode){

                    this.$emit('change', this.selectedRow.key)
                } else {

                    this.$emit('change', JSON.stringify(_.map(_.filter(this.rows, (r)=>r.isSelected),(z)=>z.key)));
                }
            },
            toggleOpened(){

                this.opened = !this.opened;
            },
            selectItem(row){

                if (this.isSingleMode) {

                    this.selectedRow = row;

                    this.opened = false;
                } else {

                    this.toggleRowSelected(row);
                }

                this.emitChanges();
            },
            parseValue(){
                if (this.field.additional && this.field.additional.values){

                    if (!this.rows.length) this.rows = _.cloneDeep(this.field.additional.values);

                    if (this.value){

                        if (this.isSingleMode) {

                            this.selectedRow = _.find(this.rows,(r)=> r.key == this.value );
                        } else {

                            let valArr;



                            try {
                                valArr = _.isString(this.value) ?  JSON.parse(this.value) : this.value;
                            } catch(e){
                            }

                            if (valArr) {

                                this.rows.forEach((r)=>{

                                    if (valArr.indexOf(r.key)>=0)

                                        this.toggleRowSelected(r);
                                });
                            }
                        }
                    }
                }
            },
        },

        beforeMount(){

        },
        mounted() {

            this.parseValue();

        },
        destroyed(){
        }
    }
</script>
