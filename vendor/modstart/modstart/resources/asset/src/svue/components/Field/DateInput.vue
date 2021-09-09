<template>
    <div>
        <el-date-picker v-model="datav" type="date" value-format="yyyy-MM-dd" :placeholder="placeholder" style="width:12em;"></el-date-picker>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "DateInput",
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
                // console.log('update', newValue, this.data, this.datav)
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
