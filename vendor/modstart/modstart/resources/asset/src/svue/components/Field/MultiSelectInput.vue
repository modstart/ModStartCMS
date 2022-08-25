<template>
    <div>
        <el-select :value="data" @change="onDataChange" multiple :placeholder="placeholder" style="width:100%">
            <el-option
                    v-for="(item,itemIndex) in option"
                    :key="itemIndex"
                    :label="item"
                    :value="item">
            </el-option>
        </el-select>
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";


    export default {
        name: "MultiSelectInput",
        mixins: [FieldInputMixin],
    }
</script>
