<template>
    <div>
        <el-input-number v-model="datav" :precision="2" :step="0.1"></el-input-number>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "DecimalFilter",
        mixins: [FieldFilterMixin],
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
