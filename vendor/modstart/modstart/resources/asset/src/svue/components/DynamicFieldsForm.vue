<script>
export default {
    name: 'DynamicFieldsForm',
    props: {
        fields: {
            type: Array,
            default: () => [],
        },
        value: {
            type: Object,
            default: () => ({}),
        },
    },
    watch: {
        fields: {
            immediate: true,
            handler(n) {
                // initialize defaults while preserving any existing values from `value`
                const fv = Object.assign({}, this.value || {});
                n.forEach(f => {
                    if (fv[f.name] === undefined) {
                        fv[f.name] = this.getDefaultValue(f);
                    }
                });
                this.formValue = fv;
            }
        },
        value: {
            deep: true,
            handler(v) {
                // keep local formValue in sync when parent updates
                this.formValue = Object.assign({}, v || {});
            }
        },
    },
    data() {
        return {
            formValue: {},
        }
    },
    methods: {
        getDefaultValue(f) {
            switch (f.type) {
                case 'checkbox':
                case 'files':
                    return f.defaultValue === null ? [] : f.defaultValue;
                case 'switch':
                    return f.defaultValue === null ? false : f.defaultValue;
            }
            return f.defaultValue !== undefined ? f.defaultValue : null;
        },
        getValue(){
            return this.formValue;
        }
    }
}
</script>

<template>
    <div>
        <div v-for="f in fields" :key="f.name" class="line">
            <div class="label">
                <span v-if="f.isRequired">*</span>
                {{ f.title }}
            </div>
            <div class="field">
                <el-input v-if="f.type==='text'" v-model="formValue[f.name]"></el-input>
                <el-input v-else-if="f.type==='textarea'" type="textarea" :rows="4"
                          v-model="formValue[f.name]"></el-input>
                <el-input-number v-else-if="f.type==='number'" v-model="formValue[f.name]"></el-input-number>
                <el-switch v-else-if="f.type==='switch'" v-model="formValue[f.name]"></el-switch>
                <el-radio-group v-else-if="f.type==='radio'" v-model="formValue[f.name]">
                    <el-radio v-for="(o, idx) in (f.data && f.data.options || [])" :key="idx" :label="o.title">
                        {{ o.title }}
                    </el-radio>
                </el-radio-group>
                <el-select v-else-if="f.type==='select'" v-model="formValue[f.name]">
                    <el-option v-for="(o, idx) in (f.data && f.data.options || [])" :key="idx" :label="o.title"
                               :value="o.title"></el-option>
                </el-select>
                <el-checkbox-group v-else-if="f.type==='checkbox'" v-model="formValue[f.name]">
                    <el-checkbox v-for="(o, idx) in (f.data && f.data.options || [])" :key="idx" :label="o.title">
                        {{ o.title }}
                    </el-checkbox>
                </el-checkbox-group>
                <file-selector v-else-if="f.type==='file'" v-model="formValue[f.name]"
                               :upload-text="f.data && f.data.text1 ? f.data.text1 : ''"
                               :gallery-enable="!!(f.data && f.data.switch1)"
                               upload-enable
                ></file-selector>
                <files-selector v-else-if="f.type==='files'" v-model="formValue[f.name]"
                                :upload-text="f.data && f.data.text1 ? f.data.text1 : ''"
                                :gallery-enable="!!(f.data && f.data.switch1)"
                                upload-enable
                ></files-selector>
                <div v-else>
                    暂未支持 {{ f.type }}
                    <code>{{ JSON.stringify(f) }}</code>
                </div>
                <div class="help" v-if="f.placeholder">{{ f.placeholder }}</div>
            </div>
        </div>
    </div>
</template>
