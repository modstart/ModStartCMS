<template>
    <div>
        <el-input-number v-model="datav" :precision="2" :step="0.1" :placeholder="placeholder"></el-input-number>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "DecimalInput",
        mixins: [FieldInputMixin],
        data() {
            return {
                datav: null,
            }
        },
        mounted() {
            if (!this.data) {
                this.datav = parseFloat(this.defaultValue)
            } else {
                this.datav = parseFloat(this.data)
            }
        },
        methods: {},
        watch: {
            datav(newValue, oldValue) {
                if (newValue !== this.data) {
                    this.$emit('update', newValue)
                }
            },
            data(newValue, oldValue) {
                if (newValue !== this.datav) {
                    this.datav = parseFloat(newValue)
                }
            },
        }
    }
</script>
