<template>
    <h1>testes</h1>
    <button
        type="button"
        @click="increment"
        class="p-2 text-white bg-gray-500 rounded"
    >
        Counter is: {{ counter }}
    </button>
</template>

<script>
export default {
    name: 'CommissionCalculator',
    data() {
        return {
            counter: 0
        };
    },
    methods: {
        increment() {
            this.counter++;
        }
    }
};
</script>
