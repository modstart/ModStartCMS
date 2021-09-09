<template>
    <div>
        <el-date-picker v-model="datav" type="datetime" value-format="yyyy-MM-dd HH:mm:ss" :placeholder="placeholder"></el-date-picker>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "DatetimeInput",
        mixins: [FieldInputMixin],
        data() {
            return {
                datav: null,
            }
        },
        mounted() {
            if (!this.data) {
                let defaultDate = new Date(this.defaultValue)
                if (defaultDate.toString() !== 'Invalid Date') {
                    this.datav = this.defaultValue
                }
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
                if (JSON.stringify(newValue) !== JSON.stringify(this.datav)) {
                    this.datav = newValue
                }
            },
        }
    }
</script>
