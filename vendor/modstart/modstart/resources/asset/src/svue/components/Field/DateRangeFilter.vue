<template>
    <div>
        <el-date-picker v-model="datav"
                        type="daterange"
                        range-separator="至"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期"
                        value-format="yyyy-MM-dd"></el-date-picker>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "DateRangeFilter",
        mixins: [FieldFilterMixin],
        data() {
            return {
                datav: [],
            }
        },
        watch: {
            datav(newValue, oldValue) {
                if (JSON.stringify(newValue) !== JSON.stringify(this.data)) {
                    this.$emit('update', newValue)
                }
            },
            data: {
                handler(newValue, oldValue) {
                    if (JSON.stringify(newValue) !== JSON.stringify(this.datav)) {
                        this.datav = newValue
                    }
                },
                deep: true,
                immediate: true
            },
        }
    }
</script>
