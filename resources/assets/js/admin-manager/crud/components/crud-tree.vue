<template>
    <div class="crud-tree">
        <crud-tree-item v-for="item in children" :siblings="children"
                        :key="item[crud.id]" :item="item" :items="items" :crud="crud"
                        @edit="onEditItem" @delete="onDeleteItem" @select="onSelect" @change="onChange"></crud-tree-item>
    </div>
</template>

<script>
    import CrudTypes from './../../utils/types';

    export default {
        name: 'crud-tree',
        props: ['items', 'crud', 'type'] ,
        data: function () {
            return {
                tree: [],
                rootItems: [],
                orderedItems: [],
                drake: {}
            }
        },
        computed: {
            children(){
                return _.filter(this.items, (i)=>!i.parent_id);
            },
            isPickMode(){ return this.type !== CrudTypes.BROWSE; },
        },
        methods: {
            buildTree(){

            },

            onEditItem(item){
                this.$emit('edit', item);
            },

            onDeleteItem(item){
                this.$emit('delete', item);
            },

            onSelect(item){
                console.log('tree-item');
                console.log(item);

                if (this.isPickMode){

                    if (this.type === CrudTypes.PICK){
                        _.map(this.items, (i)=> {
                            if (i[this.crud.id] !== item[this.crud.id])
                                this.$set(i, 'isSelected', false);
                        });
                    }

                    this.$set(item, 'isSelected', !item.isSelected);

                    this.$emit("select", _.filter(this.items, (i) => i.isSelected ));

                }
            },
            onChange(items){
                this.$emit("change", items);
            }
        },
        beforeDestroy(){

        },
        mounted() {



        }
    }
</script>
