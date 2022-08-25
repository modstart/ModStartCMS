<template>
    <div>
        <RichEditor :data="data" @input="onDataChange" :server="server" />
    </div>
</template>

<script>

    import {FieldInputMixin} from "../../lib/fields-config";
    import RichEditor from "../RichEditor";

    export default {
        name: "RichTextInput",
        components: {RichEditor},
        mixins: [FieldInputMixin],
        props: {
            server: {
                type: String,
                default: '/member_data/ueditor'
            },
        },
    }
</script>
