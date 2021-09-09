<template>
    <div>
        <el-input v-model="datav"></el-input>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "TextFilter",
        mixins: [FieldFilterMixin],
        data() {
            return {
                datav: null,
            }
        },
        mounted() {
            if (!this.data) {
                this.datav = this.defaultValue
            } else {
                this.datav = this.data
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
                    this.datav = newValue
                }
            },
        }
    }
</script>
