<div class="line">
    <div class="label">
        <span>*</span>
        字段长度
    </div>
    <div class="field">
        <el-input-number v-model="data.maxLength"></el-input-number>
    </div>
</div>
<div class="line">
    <div class="label">
        <span>*</span>
        选项
    </div>
    <div class="field">
        <table class="ub-table mini tw-bg-white">
            <thead>
            <tr>
                <th>值</th>
                <th>&nbsp;</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(option,optionIndex) in data.fieldData.options">
                <td>
                    <el-input v-model="data.fieldData.options[optionIndex]" placeholder="输入值"></el-input>
                </td>
                <td>
                    <a href="javascript:;" class="ub-text-danger"
                       @click="data.fieldData.options.splice(optionIndex,1)">
                        <i class="iconfont icon-trash"></i>
                    </a>
                </td>
            </tr>
            </tbody>
            <tbody>
            <tr>
                <td colspan="2">
                    <a href="javascript:;" class="ub-text-muted" @click="data.fieldData.options.push('')">
                        <i class="iconfont icon-plus"></i>
                    </a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
