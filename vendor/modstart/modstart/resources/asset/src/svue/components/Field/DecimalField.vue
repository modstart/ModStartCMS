<template>
    <div>
        {{data}}
    </div>
</template>

<script>
    import {FieldViewMixin} from "../../lib/fields-config";

    export default {
        name: 'DecimalField',
        mixins: [FieldViewMixin],
    }
</script>
