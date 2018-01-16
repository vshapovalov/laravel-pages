import VueTestComponent from './test-component.vue';

class TestComponent {
    constructor(){
        this.instance = null;
    }

    createComponent(){

        this.instance = new Vue({
            template: '<test-component v-if="active"></test-component>',
            data:{
                active: true
            },
            watch:{
                active: {
                    handler(val, oldVal){
                        if (!val){
                            this.$nextTick(()=>{
                                this.$destroy();
                            });
                        }
                    }
                }
            },
            components: {
                'test-component': VueTestComponent
            }
        });
    }

    // must be implemented
    show(elementId){

        this.createComponent();

        this.instance.$mount('#' + elementId);
    }

    // must be implemented
    close(){
        this.instance.active = false;
    }
}

// register component creating bus event
Bus.$on('user:testcomponent:mount', ()=> Bus.$emit('admin:instance:mount', new TestComponent()) );

