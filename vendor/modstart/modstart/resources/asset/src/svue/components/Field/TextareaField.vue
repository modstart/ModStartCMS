<template>
    <div>
        {{data}}
    </div>
</template>

<script>
    import {FieldViewMixin} from "../../lib/fields-config";

    export default {
        name: 'TextareaField',
        mixins: [FieldViewMixin],
    }
</script>
