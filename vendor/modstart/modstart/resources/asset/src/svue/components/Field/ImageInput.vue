<template>
    <div>
        <ImageSelector v-model="datav"/>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";
    import ImageSelector from "../ImageSelector";

    export default {
        name: "ImageInput",
        components: {ImageSelector},
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
