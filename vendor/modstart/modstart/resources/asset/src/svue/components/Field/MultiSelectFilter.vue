<template>
    <div>
        <el-select v-model="datav" multiple>
            <el-option
                    v-for="(item,itemIndex) in field.option"
                    :key="itemIndex"
                    :label="item"
                    :value="item">
            </el-option>
        </el-select>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "MultiSelectFilter",
        mixins: [FieldFilterMixin],
        data() {
            return {
                datav: [],
            }
        },
        watch: {
            datav(newValue, oldValue) {
                if (newValue !== this.data) {
                    this.$emit('update', newValue)
                }
            },
            data: {
                handler(newValue, oldValue) {
                    if (newValue !== this.datav) {
                        this.datav = newValue
                    }
                },
                immediate: true
            },
        }
    }
</script>
