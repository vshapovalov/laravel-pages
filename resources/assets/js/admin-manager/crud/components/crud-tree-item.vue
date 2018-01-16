<template>
    <div class="crud-tree-item ">
        <div>
            <div class="box is-paddingless has-text-centered" :class="{'notification is-primary': item.isSelected}">
                <div>
                    <a v-show="item.parent_id" class=" " @click.prevent.stop="onUp">
                        <i class="fa fa-arrow-up"></i>
                    </a>
                </div>
                <div class="columns is-marginless">
                    <div class="column">
                        <a v-show="item.level > 2" class=" " @click.prevent.stop="onLeft">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </div>
                    <div class="column is-narrow">
                        <div>
                            <a class="" @click="onSelect(item)">{{ item[crud.display] }}</a>
                        </div>
                        <div>
                            <a class="button is-warning is-outlined " @click.prevent.stop="onEditItem(item)">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a class="button is-danger is-outlined " @click.prevent.stop="onDeleteItem(item)">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>

                    <div class="column">
                        <a v-show="item.parent_id" class=" " @click.prevent.stop="onRight">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="has-text-centered">
                    <a v-show="item.parent_id" class=" " @click.prevent.stop="onDown">
                        <i class="fa fa-arrow-down"></i>
                    </a>
                </div>
            </div>

        </div>
        <crud-tree-item v-for="item in children" :siblings="children" :key="item[crud.id]" :items="items"
                        :item="item" :crud="crud" @edit="onEditItem" @delete="onDeleteItem"
                        @select="onSelect" @change="onChange"></crud-tree-item>
    </div>
</template>

<script>
    import VueCrudTreeItem from './crud-tree-item.vue';
    import CrudUtils from './../utils';


    export default {
        name: 'crud-tree-item',
        props: {
            item:{},
            items:{},
            crud:{},
            siblings:{},
        },
        data: function () {
            return {}
        },
        components:{
            'crud-tree-item': VueCrudTreeItem
        },
        computed: {
            children(){
                return _.orderBy(_.filter(this.items, (i)=>i.parent_id === this.item[this.crud.id]), ['order'], ['asc']);
            }
        },
        methods: {

            onEditItem(item){
                this.$emit('edit', item);
            },
            onDeleteItem(item){
                this.$emit('delete', item);
            },
            onSelect(item){
                this.$emit('select', item);
            },
            getParent(){
              return _.find(this.items, (i)=>i[this.crud.id] === this.item.parent_id);
            },
            onLeft(){
                let parentItem = this.getParent();

                if (parentItem){
                    this.item.parent_id = parentItem.parent_id;
                    this.item.parent = parentItem.parent;
                    this.item.level = parentItem.level;

                    this.item.order = CrudUtils.getItemsMaxOrderByLevel(this.item.level, this.item[this.crud.id], this.items) + 1;
                    this.onChange([this.item]);
                }


            },
            onChange(items){
                this.$emit("change", items);
            },
            onRight(){

                let prevSibling = CrudUtils.getPrevTreeSiblingById(this.item[this.crud.id], this.siblings);

                if (prevSibling){
                    this.item.parent_id = prevSibling[this.crud.id];
                    this.item.parent = prevSibling.parent;
                    this.item.level += 1;

                    this.item.order = CrudUtils.getItemsMaxOrderByLevel(this.item.level, this.item[this.crud.id], this.items) + 1;

                    this.onChange([this.item]);
                }

            },
            onUp(){

                let prevSibling = CrudUtils.getPrevTreeSiblingById(this.item[this.crud.id], this.siblings);

                if (prevSibling) {

                    let siblingOrder = prevSibling.order;

                    prevSibling.order = this.item.order;

                    this.item.order = siblingOrder;

                    this.onChange([this.item, prevSibling]);
                }

            },
            onDown(){

                let nextSibling = CrudUtils.getNextTreeSiblingById(this.item[this.crud.id], this.siblings);

                if (nextSibling) {

                    let siblingOrder = nextSibling.order;

                    nextSibling.order = this.item.order;

                    this.item.order = siblingOrder;

                    this.onChange([this.item, prevSibling]);
                }
            },
        },
        mounted() {

        }
    }
</script>
