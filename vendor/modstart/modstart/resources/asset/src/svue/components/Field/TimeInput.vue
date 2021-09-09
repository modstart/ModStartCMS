<template>
    <div>
        <el-time-picker
                v-model="datav"
                style="width:10em;"
                :picker-options="{selectableRange: '00:00:00 - 23:59:00'}"
                value-format="HH:mm:ss"
                :placeholder="placeholder"></el-time-picker>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "TimeInput",
        mixins: [FieldInputMixin],
        data() {
            return {
                datav: null,
            }
        },
        mounted() {
            if (!this.data) {
                let defaultDate = new Date('2019-01-01 ' + this.defaultValue)
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
