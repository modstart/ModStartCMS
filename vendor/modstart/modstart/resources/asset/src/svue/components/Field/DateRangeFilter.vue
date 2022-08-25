<template>
    <div>
        <el-date-picker :value="data"
                        @change="onDataChange"
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
    }
</script>
