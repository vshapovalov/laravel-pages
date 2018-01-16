<template>
    <div class="menu-item" :class="{opened: opened, 'has-items': item.items && item.items.length > 0, 'is-active': item.active}">
        <a @click.stop.prevent="onClick">{{ item.caption }}</a>
        <transition name="slide">
            <div v-show="opened" class="nested-items">
                <menu-item key="nestedItem.name" v-for="nestedItem in item.items" :item="nestedItem" @selected="onSelected"></menu-item>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: 'menu-item',
        props: ['item'],
        data: function () {
            return {
                opened: false
            }
        },
        computed: {},
        methods: {
            onClick(){
                if (this.item.action){
                    this.onSelected(this.item)
                } else {
                    if (this.item.items && this.item.items.length > 0) {
                        this.toggleOpened();
                    }
                }
            },
            onSelected(item){
                this.$emit('selected', item);
            },
            toggleOpened(){
                this.opened = !this.opened;
            }
        },
        mounted() {

        }
    }
</script>

<style>
    .nested-items{
        overflow: hidden;
        max-height: 300px;
    }

    .slide-enter, .slide-leave-to{
        max-height: 0;
    }

    .slide-enter-active, .slide-leave-active{
        transition: max-height 0.8s;
    }
</style>