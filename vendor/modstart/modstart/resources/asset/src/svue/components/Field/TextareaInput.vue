<template>
    <div>
        <el-input :value="data" @input="onDataChange" :placeholder="placeholder" type="textarea" :rows="2"></el-input>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";

    export default {
        name: "TextareaInput",
        mixins: [FieldInputMixin],
    }
</script>
