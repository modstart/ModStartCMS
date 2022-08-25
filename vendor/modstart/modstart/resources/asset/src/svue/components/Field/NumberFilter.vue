<template>
    <div>
        <el-input-number :value="data" @change="onDataChange"></el-input-number>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";

    export default {
        name: "NumberFilter",
        mixins: [FieldFilterMixin],
    }
</script>
