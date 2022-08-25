<template>
    <div>
        <el-input-number :value="data" @change="onDataChange" :precision="2" :step="0.1"></el-input-number>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";


    export default {
        name: "DecimalFilter",
        mixins: [FieldFilterMixin],
    }
</script>
