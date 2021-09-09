<template>
    <div>
        <el-time-picker
                is-range
                v-model="datav"
                range-separator="至"
                start-placeholder="开始时间"
                end-placeholder="结束时间"
                :picker-options="{selectableRange: '00:00:00 - 23:59:00'}"
                value-format="HH:mm:ss"></el-time-picker>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "TimeRangeFilter",
        mixins: [FieldFilterMixin],
        data() {
            return {
                datav: ['', ''],
            }
        },
        methods: {},
        watch: {
            datav(newValue, oldValue) {
                if (newValue !== this.data) {
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
            }
        }
    }
</script>
