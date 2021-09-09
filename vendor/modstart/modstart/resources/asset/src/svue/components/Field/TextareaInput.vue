<template>
    <div>
        <el-input v-model="datav" :placeholder="placeholder" type="textarea" :rows="2"></el-input>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";

    export default {
        name: "TextareaInput",
        mixins: [FieldInputMixin],
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
