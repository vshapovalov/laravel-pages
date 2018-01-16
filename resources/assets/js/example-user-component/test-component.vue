<template>
    <div class="">
        <p>{{ message }} {{ counter }}</p>
        <div>
            <a @click.stop.prevent="counter++" class="button is-warning is-outlined">Increment counter</a>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'test-component',
        props: {},
        data: function () {
            return {
                message: 'Hello, i\'m test component, and this is my counter -',
                counter: 1
            }
        },
        computed: {},
        methods: {},
        mounted() {

        }
    }
</script>
