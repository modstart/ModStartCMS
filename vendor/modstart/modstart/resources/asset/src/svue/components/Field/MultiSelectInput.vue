<template>
    <div>
        <el-select v-model="datav" multiple :placeholder="placeholder" style="width:100%">
            <el-option
                    v-for="(item,itemIndex) in option"
                    :key="itemIndex"
                    :label="item"
                    :value="item">
            </el-option>
        </el-select>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "MultiSelectInput",
        mixins: [FieldInputMixin],
        data() {
            return {
                datav: [],
            }
        },
        mounted() {
            if (!this.data) {
                if (Array.isArray(this.defaultValue)) {
                    this.datav = this.defaultValue
                }
            } else {
                if (Array.isArray(this.data)) {
                    this.datav = this.data
                }
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
