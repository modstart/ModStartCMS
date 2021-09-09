<template>
    <div>
        <div v-html="data"></div>
    </div>
</template>

<script>
    import {FieldViewMixin} from "../../lib/fields-config";

    export default {
        name: 'RichTextField',
        mixins: [FieldViewMixin],
    }
</script>
