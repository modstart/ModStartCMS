<template>
    <div>
        <el-input :value="data" @input="onDataChange"></el-input>
    </div>
</template>

<script>

    import {FieldFilterMixin} from "../../lib/fields-config";

    export default {
        name: "TextFilter",
        mixins: [FieldFilterMixin],
    }
</script>
