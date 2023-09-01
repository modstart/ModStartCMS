import {Storage} from "./storage";

export const FieldInputFilter = (type) => {
    switch (type) {
        default:
            return type + 'Input'
    }
}

export const FieldFieldFilter = (type) => {
    switch (type) {
        default:
            return type + 'Field'
    }
}

const FieldWidthDefaults = {
    _checkbox: 40,
    Datetime: 160,
    Time: 80,
    Date: 100,
    Number: 80,
    Decimal: 80,
}

export const VModelMixin = {
    model: {
        prop: 'value',
        event: 'input'
    },
    computed: {
        modelValue: {
            get() {
                return this.value
            },
            set(value) {
                this.onInput(value)
            }
        }
    },
    methods: {
        onInput(value) {
            if (typeof value === 'object') {
                if (value.type && value.target && value.detail) {
                    value = value.detail.value
                }
            }
            this.$emit("input", value)
        }
    },
    props: {
        value: {
            type: [Number, String, Object, Boolean, Array],
            default: null
        },
    }
}

/**
 * @deprecated use VModelMixin
 */
export const FieldVModel = {
    model: {
        prop: 'data',
        event: 'update'
    },
    computed: {
        currentData: {
            get() {
                return this.data
            },
            set(value) {
                this.$emit("update", value)
            }
        }
    },
    props: {
        data: {},
    }
}

/**
 * @deprecated use VModelMixin
 */
export const FieldInputMixin = {
    model: {
        prop: 'data',
        event: 'update'
    },
    props: {
        data: {
            type: [String, Number, Boolean, Object, Array, null ],
            default: () => {
                return null
            }
        },
        isRequired: {
            type: Boolean,
            default: false,
        },
        option: {
            type: Array,
            default: () => {
                return []
            }
        },
        textLength: {
            type: Number,
            default: 50
        },
        placeholder: {
            type: String,
            default: ''
        },
        defaultValue: {}
    },
    methods: {
        onDataChange(v){
            // console.log('onDataChange',v)
            this.$emit("update", v)
        }
    }
}


/**
 * @deprecated use VModelMixin
 */
export const FieldFilterMixin = {
    model: {
        prop: 'data',
        event: 'update'
    },
    props: {
        data: {
            type: [String, Number, Boolean, Object, Array, null ],
            default: () => {
                return null
            }
        },
        field: {},
    },
    methods: {
        onDataChange(v){
            // console.log('onDataChange', v)
            this.$emit("update", v)
        }
    }
}

export const FieldViewMixin = {
    props: {
        data: {}
    },
}

export const FieldWidthManager = {
    calculators: {},
    modifiers: {},
    datas: {},
    getData(name) {
        if (!(name in FieldWidthManager.datas)) {
            FieldWidthManager.datas[name] = Storage.getObject(`FieldWidth_${name}`, {})
        }
        if (!FieldWidthManager.datas[name]) {
            FieldWidthManager.datas[name] = {}
        }
        return FieldWidthManager.datas[name]
    },
    setData(name, fieldName, width) {
        let data = FieldWidthManager.getData(name)
        data[fieldName] = width
        Storage.set(`FieldWidth_${name}`, data)
    },
    buildCalculator(name) {
        let data = FieldWidthManager.getData(name)
        if (!(name in FieldWidthManager.calculators)) {
            FieldWidthManager.calculators[name] = function (fieldName, defaultWidth, fieldType) {
                fieldType = fieldType || '_NONE_'
                let w = 0
                try {
                    w = data[fieldName]
                } catch (e) {
                }
                if (!w) {
                    if (fieldType in FieldWidthDefaults) {
                        w = FieldWidthDefaults[fieldType]
                    } else {
                        w = defaultWidth
                    }
                }
                return w
            }
        }
        return FieldWidthManager.calculators[name]
    },
    buildModifier(name) {
        let data = FieldWidthManager.getData(name)
        if (!(name in FieldWidthManager.modifiers)) {
            FieldWidthManager.modifiers[name] = function (fieldName, width) {
                data[fieldName] = width
                FieldWidthManager.setData(name, fieldName, width)
            }
        }
        return FieldWidthManager.modifiers[name]
    },
}

export const FieldFilterManager = {
    conditionVisible(type) {
        return FieldFilterManager.condition(type).length > 0
    },
    conditionDefault(type) {
        let conditions = FieldFilterManager.condition(type)
        if (conditions.length > 0) {
            return conditions[0].value
        }
        return null
    },
    view(type, condition, value) {
        const map = {
            is: '等于',
            is_not: '不等于',
            contains: '包含',
            not_contains: '不包含',
            range: '范围',
            is_empty: '为空',
            is_not_empty: '不为空',
            gt: '大于',
            egt: '大于等于',
            lt: '小于',
            elt: '小于等于',
        }
        let views = []
        if ((condition in map)) {
            views.push(map[condition])
        }
        switch (type) {
            case 'Text':
            case 'Textarea':
            case 'RichText':
                views.push(value)
                break;
            case 'Datetime':
            case 'Time':
            case 'Date':
                views.push(value[0] + '至' + value[1])
                break;
            case 'Number':
            case 'Decimal':
                if (!['is_empty', 'is_not_empty'].includes(condition)) {
                    views.push(value)
                }
                break;
            case 'Select':
                views.push(value)
                break;
            case 'MultiSelect':
                views.push(value.join(','))
                break;
            case 'Image':
                break;
        }
        return views.join(' ')
    },
    condition(type) {
        switch (type) {
            case 'Text':
            case 'Textarea':
            case 'RichText':
                return [
                    {value: 'is', label: '等于'},
                    {value: 'is_not', label: '不等于'},
                    {value: 'contains', label: '包含'},
                    {value: 'not_contains', label: '不包含'}
                ]
            case 'Datetime':
            case 'Time':
            case 'Date':
                return [
                    {value: 'range', label: '范围'},
                ]
            case 'Number':
            case 'Decimal':
                return [
                    {value: 'is', label: '等于'},
                    {value: 'is_not', label: '不等于'},
                    {value: 'is_empty', label: '为空'},
                    {value: 'is_not_empty', label: '不为空'},
                    {value: 'gt', label: '大于'},
                    {value: 'egt', label: '大于等于'},
                    {value: 'lt', label: '小于'},
                    {value: 'elt', label: '小于等于'}
                ]
            case 'Select':
                return [
                    {value: 'is', label: '等于'},
                    {value: 'is_not', label: '不等于'},
                ]
            case 'MultiSelect':
                return [
                    {value: 'contains', label: '包含'},
                    {value: 'not_contains', label: '不包含'},
                ]
            case 'Image':
                return [
                    {value: 'is_empty', label: '为空'},
                    {value: 'is_not_empty', label: '不为空'},
                ]
            default:
                return []
        }
    },
    valueVisible(type, condition) {
        switch (type) {
            case 'Text':
            case 'Textarea':
            case 'RichText':
                return ['is', 'is_not', 'contains', 'not_contains'].includes(condition)
            case 'Datetime':
            case 'Time':
            case 'Date':
                return ['range',].includes(condition)
            case 'Number':
            case 'Decimal':
                return ['is', 'is_not', 'gt', 'egt', 'lt', 'elt'].includes(condition)
            case 'Select':
                return ['is', 'is_not',].includes(condition)
            case 'MultiSelect':
                return ['contains', 'not_contains',].includes(condition)
            case 'Image':
                return false
            default:
                return false
        }
    },
    value(type, condition) {
        switch (type) {
            case 'Text':
            case 'Textarea':
            case 'RichText':
                return ''
            case 'Datetime':
            case 'Time':
            case 'Date':
                return ['', '']
            case 'Number':
            case 'Decimal':
                return ''
            case 'Select':
                return ''
            case 'MultiSelect':
                return []
            case 'Image':
                return null
            default:
                return null
        }
    },
    valueComponent(type, condition) {
        switch (type) {
            case 'Text':
            case 'Textarea':
            case 'RichText':
                return type + 'Filter'
            case 'Datetime':
            case 'Time':
            case 'Date':
                return type + 'RangeFilter'
            case 'Number':
            case 'Decimal':
                return type + 'Filter'
            case 'Select':
                return type + 'Filter'
            case 'MultiSelect':
                return type + 'Filter'
            case 'Image':
                return null
            default:
                return null
        }
    }
}
