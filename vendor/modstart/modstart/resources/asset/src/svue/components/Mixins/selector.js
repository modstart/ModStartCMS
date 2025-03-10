export const VModelMixin = {
    model: {
        prop: 'value',
        event: 'update:modelValue',
    },
    props: {
        value: {
            type: [String, Number, Boolean, Object, Array],
            default: null,
        },
    },
    computed: {
        modelValue: {
            get() {
                return this.value;
            },
            set(val) {
                this.$emit('update:modelValue', val);
            },
        },
    },
}

export const SelectorRecordsMixin = {
    data() {
        return {
            loading: true,
            records: []
        }
    }
}

export const SelectorBooleanMixin = {
    model: {
        prop: 'data',
        event: 'update'
    },
    props: {
        data: null
    },
    data() {
        return {
            datav: null,
        }
    },
    watch: {
        data: {
            handler(newValue, oldValue) {
                if (!this.data && !this.datav) {
                } else if (!!this.data && !!this.datav) {
                } else {
                    this.datav = (!!this.data) ? true : false
                }
            },
            immediate: true
        },
        datav(newValue, oldValue) {
            this.$emit('update', this.datav)
        }
    },
}

export const SelectorIdMixin = {
    model: {
        prop: 'data',
        event: 'update'
    },
    props: {
        data: null
    },
    data() {
        return {
            datav: null,
        }
    },
    watch: {
        data: {
            handler(newValue, oldValue) {
                if (this.data !== this.datav) {
                    this.datav = this.data
                }
            },
            immediate: true
        },
        datav(newValue, oldValue) {
            this.$emit('update', this.datav)
        }
    },
}

export const SelectorIdsMixin = {
    model: {
        prop: 'data',
        event: 'update'
    },
    props: {
        data: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            datav: [],
        }
    },
    watch: {
        data: {
            handler(newValue, oldValue) {
                if (JSON.stringify(this.data) !== JSON.stringify(this.datav)) {
                    this.datav = this.data
                }
            },
            immediate: true
        },
        datav(newValue, oldValue) {
            this.$emit('update', this.datav)
        }
    },
}
