<template>
    <div>
        <div v-if="storeConfig.disable" class="ub-alert danger">
            <i class="iconfont icon-close-o"></i>
            当前环境禁止「模块管理」相关操作
        </div>
        <div class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            为了系统和数据安全，在线 <b>安装</b>、<b>卸载</b>、<b>升级</b> 模块前请做好代码和数据备份
        </div>
        <div v-if="d" class="ub-alert warning">
            <i class="iconfont icon-warning"></i>
            您还没有登录，登录后才能从模块市场安装、升级模块
            <a href="javascript:;" @click="doMemberLoginShow()"><i class="iconfont icon-user"></i>立即登录</a>
        </div>
        <div class="tw-bg-white tw-rounded tw-relative">
            <el-tabs v-model="search.tab" style="height:45px;">
                <el-tab-pane name="store">
                    <span slot="label">
                        <i class="iconfont icon-cart"></i> 模块市场
                    </span>
                </el-tab-pane>
                <el-tab-pane name="installed">
                    <span slot="label">
                        <i class="iconfont icon-list-alt"></i> 已安装
                    </span>
                </el-tab-pane>
                <el-tab-pane name="enabled">
                    <span slot="label">
                        <i class="iconfont icon-checked"></i> 已启用
                    </span>
                </el-tab-pane>
                <el-tab-pane name="disabled">
                    <span slot="label">
                        <i class="iconfont icon-close"></i> 已禁用
                    </span>
                </el-tab-pane>
                <el-tab-pane name="local">
                    <span slot="label">
                        <i class="iconfont icon-pc"></i> 本地模块
                        <i class="iconfont icon-warning" data-tip-popover="本地存在且模块市场不存在的模块"></i>
                    </span>
                </el-tab-pane>
                <el-tab-pane name="upgradeable">
                    <span slot="label">
                        <i class="iconfont icon-direction-up"></i> 可升级
                    </span>
                </el-tab-pane>
            </el-tabs>
            <a href="javascript:;" @click="search.tab='system'"
               class="ub-text-muted tw-leading-10 tw-px-3 tw-absolute tw-right-0 tw-top-0">
                <i class="iconfont icon-cog"></i> 系统模块
            </a>
            <div class="ub-padding">
                <div class="tw-float-right">
                    <el-button round style="padding:0.25rem 0.5rem;" :loading="memberUserLoading"
                               @click="doMemberLoginShow()">
                        <span v-if="memberUserLoading">
                            登录中
                        </span>
                        <span v-else-if="memberUser.id>0">
                            <div v-if="memberUser.avatar" class="ub-cover-1-1 tw-rounded-full"
                                 style="width:0.8rem;display:inline-block;vertical-align:middle;"
                                 :style="{backgroundImage:`url(${memberUser.avatar})`}"></div>
                            <i v-else class="iconfont icon-user"></i>
                            {{ memberUser.username }}
                        </span>
                        <span v-else>
                            <i class="iconfont icon-user"></i>
                            未登录
                        </span>
                    </el-button>
                </div>
                <el-radio-group v-model="search.priceType" v-if="search.tab!=='system'">
                    <el-radio-button label="all"><i class="iconfont icon-list-alt"></i> 全部</el-radio-button>
                    <el-radio-button label="free"><i class="iconfont icon-gift"></i> 免费</el-radio-button>
                    <el-radio-button label="fee"><i class="iconfont icon-cny"></i> 付费</el-radio-button>
                </el-radio-group>
                <el-checkbox v-model="search.isRecommend" border v-if="search.tab!=='system'">推荐</el-checkbox>
                <el-input prefix-icon="el-icon-search"
                          v-model="search.keywords"
                          style="width:10rem;"
                          placeholder="搜索模块"></el-input>
            </div>
            <div class="tw-px-2" v-if="categories.length>0 && search.tab!=='system'">
                <i class="iconfont icon-category tw-ml-1 tw-text-gray-600 tw-inline-block tw-w-4"></i>
                分类：
                <a href="javascript:;" class="tw-text-gray-500 tw-mr-1"
                   :class="{'ub-text-primary':search.categoryId===0}" @click="search.categoryId=0">
                    全部
                </a>
                <a href="javascript:;" class="tw-mr-1 tw-text-gray-500 tw-mr-1"
                   :class="{'ub-text-primary':search.categoryId===cat.id}"
                   v-for="(cat,catIndex) in categories" :key="catIndex" @click="search.categoryId=cat.id">
                    {{ cat.title }}
                </a>
            </div>
            <div class="tw-px-2 tw-pt-2" v-if="types.length>0 && search.tab!=='system'">
                <i class="iconfont icon-desktop tw-ml-1 tw-text-gray-600 tw-inline-block tw-w-4"></i>
                类型：
                <a href="javascript:;" class="tw-text-gray-500 tw-mr-1"
                   :class="{'ub-text-primary':!search.type}" @click="search.type=''">
                    全部
                </a>
                <a href="javascript:;" class="tw-mr-1 tw-text-gray-500 tw-mr-1"
                   :class="{'ub-text-primary':search.type===type.value}"
                   v-for="(type,typeIndex) in types" :key="typeIndex" @click="search.type=type.value">
                    {{ type.title }}
                </a>
            </div>
            <div class="ub-empty" v-if="loading">
                <div class="icon">
                    <i class="icon-refresh iconfont tw-animate-spin tw-inline-block tw-p-4"></i>
                </div>
                <div class="text">正在加载...</div>
            </div>
            <div class="ub-empty" v-if="!loading && !filterModules.length">
                <div class="icon">
                    <i class="iconfont icon-empty-box"></i>
                </div>
                <div class="text">暂无记录</div>
            </div>
            <div class="ub-padding" v-if="filterModules.length>0">
                <div class="row">
                    <div v-for="(module,moduleIndex) in filterModules" class="col-md-4">
                        <div
                            class="tw-bg-white tw-p-2 tw-rounded tw-mb-2 tw-border-gray-200 tw-border-solid tw-border tw-shadow">
                            <div style="padding-left:6rem;">
                                <div class="tw-w-28 tw-float-left" style="margin-left:-6rem;">
                                    <a v-if="module.url"
                                       :href="module._isSystem?'javascript:;':module.url"
                                       :target="module._isSystem?'':'_blank'"
                                       class="ub-cover-3-2 tw-shadow  tw-rounded"
                                       :class="{'tw-w-24 tw-cursor-default':module._isSystem,'tw-w-24':!module._isSystem}"
                                       :style="{'background-image':'url('+module.cover+')'}"></a>
                                    <div v-else
                                         class="tw-shadow tw-w-24 tw-h-16 tw-rounded ub-text-center tw-text-white tw-bg-blue-300">
                                        <i class="iconfont icon-cube"
                                           style="font-size:1.6rem;line-height:3.2rem;"></i>
                                    </div>
                                </div>
                                <div>
                                    <a :href="module._isSystem?'javascript:;':module.url"
                                       :target="module._isSystem?'':'_blank'"
                                       :class="{'tw-cursor-default':module._isSystem}"
                                       class="tw-font-bold tw-text-gray-700 ub-text-truncate tw-block">
                                        <span v-if="module._isLocal" class="ub-tag primary sm ub-bg-a">内置</span>
                                        <span v-html="$highlight(module.title,search.keywords)"></span>
                                    </a>
                                    <div v-if="!module._isSystem">
                                        <span v-if="!module.isFee && !module._isLocal"
                                              class="ub-text-success">免费</span>
                                        <div v-if="module.isFee" class="ub-text-danger">
                                            <span v-if="module.priceYearEnable">￥{{ module.priceYear }}</span>
                                            <span v-else-if="module.priceSuperEnable">￥{{ module.priceSuper }}</span>
                                        </div>
                                    </div>
                                    <div class="tw-text-gray-400 tw-text-sm tw-mt-2"
                                         style="height:2rem;overflow:auto;"
                                         v-html="$highlight(module.description,search.keywords)"></div>
                                </div>
                            </div>
                            <div v-if="!module._isSystem"
                                 class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-100 tw-mt-2 tw-pt-2 tw-text-gray-500">
                                <div class="tw-float-right" v-if="module._isInstalled">
                                    <a v-if="module._isInstalled && module._hasConfig" href="javascript:;"
                                       :data-tk-event="'ModuleStore,Config,'+module.name"
                                       @click="doConfig(module)">
                                        <i class="iconfont icon-cog"></i> 配置
                                    </a>
                                </div>
                                <a v-if="!module._isInstalled" href="javascript:;" @click="doInstall(module)"
                                   class="tw-mr-1" :data-tk-event="'ModuleStore,Install,'+module.name"
                                >
                                    <i class="iconfont icon-plus"></i> 安装
                                </a>
                                <el-tooltip class="item" effect="dark" content="安装其他版本" placement="top">
                                    <a v-if="!module._isInstalled" href="javascript:;" @click="doInstallVersion(module)"
                                       class="tw-mr-4 tw-text-gray-400"
                                    >
                                        <i class="iconfont icon-down"></i>
                                    </a>
                                </el-tooltip>
                                <a v-if="module._isInstalled && !module._isLocal && canUpgrade(module._localVersion,module.latestVersion)"
                                   @click="doUpgrade(module)"
                                   :data-tk-event="'ModuleStore,Upgrade,'+module.name"
                                   href="javascript:;" class="ub-text-warning tw-mr-4">
                                    <i class="iconfont icon-direction-up"></i> 升级
                                </a>
                                <a v-if="module._isInstalled && module._isEnabled" href="javascript:;"
                                   @click="doDisable(module)"
                                   :data-tk-event="'ModuleStore,Disable,'+module.name"
                                   class="ub-text-danger tw-mr-4">
                                    <i class="iconfont icon-pause"></i> 禁用
                                </a>
                                <a v-if="module._isInstalled && !module._isEnabled" href="javascript:;"
                                   class="tw-mr-4"
                                   :data-tk-event="'ModuleStore,Enable,'+module.name"
                                   @click="doEnable(module)"
                                >
                                    <i class="iconfont icon-play"></i> 启用
                                </a>
                                <a v-if="module._isInstalled && !module._isEnabled" href="javascript:;"
                                   :data-tk-event="'ModuleStore,Uninstall,'+module.name"
                                   @click="doUninstall(module)"
                                   class="ub-text-danger tw-mr-4">
                                    <i class="iconfont icon-trash"></i> 卸载
                                </a>
                            </div>
                            <div v-else
                                 class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-100 tw-mt-2 tw-pt-2 tw-text-gray-500 tw-overflow-hidden">
                                <div class="tw-float-right" v-if="module._isInstalled">
                                    <a v-if="module._isInstalled && module._hasConfig" href="javascript:;"
                                       :data-tk-event="'ModuleStore,Config,'+module.name"
                                       @click="doConfig(module)">
                                        <i class="iconfont icon-cog"></i> 配置
                                    </a>
                                </div>
                                <div v-if="module._isSystem" class="ub-text-muted tw-inline-block"><i
                                    class="iconfont icon-tag"></i><span
                                    v-html="$highlight(module.name,search.keywords)"></span></div>
                                <a v-if="module._isInstalled && !module._isLocal && module.latestVersion && versionCompare(module.latestVersion,module._localVersion)>0"
                                   :data-tk-event="'ModuleStore,UpgradeSystem,'+module.name"
                                   @click="doUpgrade(module)"
                                   href="javascript:;" class="ub-text-warning tw-mr-4">
                                    <i class="iconfont icon-direction-up"></i> 升级
                                </a>
                            </div>
                            <div v-if="!module._isSystem"
                                 class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-100 tw-mt-2 tw-pt-2 tw-text-gray-500">
                                <div class="ub-text-muted tw-inline-block"><i class="iconfont icon-tag"></i><span
                                    v-html="$highlight(module.name,search.keywords)"></span></div>
                                <span class="ub-text-muted">|</span>
                                <span class="ub-text-muted" v-if="module._isLocal">
                                    版本V{{ module._localVersion }}
                                </span>
                                <span class="ub-text-muted" v-else-if="module._isInstalled">
                                    已安装V{{ module._localVersion }}，最新版V{{ module.latestVersion }}
                                </span>
                                <span class="ub-text-muted" v-else>
                                    最新版V{{ module.latestVersion }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog :visible.sync="memberUserShow" append-to-body custom-class="pb-member-info-dialog">
            <div slot="title">
                <a href="https://modstart.com" target="_blank">
                    <img class="tw-h-8" :src="$url.cdn('vendor/ModuleStore/image/logo_modstart.png')"/>
                </a>
            </div>
            <div v-if="!memberUser.id">
                <div style="padding:0 1.5rem;">
                    <div class="tw-py-2 tw-text-center tw-text-lg">
                        请登录账号
                    </div>
                    <div class="ub-form vertical">
                        <div class="line">
                            <div class="label">用户名</div>
                            <div class="field">
                                <input type="text" class="form" v-model="memberLoginInfo.username"
                                       @keyup="doSubmitCheck" placeholder="输入用户名"/>
                            </div>
                        </div>
                        <div class="line">
                            <div class="label">密码</div>
                            <div class="field">
                                <input type="password" class="form" v-model="memberLoginInfo.password"
                                       @keyup="doSubmitCheck"
                                       placeholder="输入密码"/>
                            </div>
                        </div>
                        <div class="line">
                            <div class="label">验证码</div>
                            <div class="field">
                                <div class="row no-gutters">
                                    <div class="col-8">
                                        <input type="text" class="form" v-model="memberLoginInfo.captcha"
                                               autocomplete="off" @keyup="doSubmitCheck"
                                               placeholder="图片验证码"/>
                                    </div>
                                    <div class="col-4">
                                        <img class="captcha" title="刷新验证" :src="memberLoginCaptchaImage"
                                             @click="doMemberLoginCaptchaRefresh()"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="line">
                            <div class="field">
                                <el-checkbox v-model="memberLoginInfo.agree">
                                    同意
                                    <a target="_blank" href="https://modstart.com/article/module_agreement">《使用协议》</a>
                                    <a target="_blank" href="https://modstart.com/article/disclaimer">《免责声明》</a>
                                </el-checkbox>
                            </div>
                        </div>
                        <div class="line">
                            <div class="field">
                                <button type="submit" class="btn btn-primary btn-block" @click="doMemberLoginSubmit()">
                                    登录
                                </button>
                            </div>
                        </div>
                        <div class="line">
                            <div class="field">
                                <div class="tw-float-right">
                                    <a href="javascript:;" style="color:#19B84D;" @click="doScanLogin()">
                                        <i class="iconfont icon-wechat"></i>
                                        微信扫一扫
                                    </a>
                                </div>
                                还没有账号？<a href="https://modstart.com" target="_blank">立即注册</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div>
                    <div
                        class="tw-bg-white tw-rounded-sm tw-mb-2 tw-box tw-px-5 tw-py-3 tw-mb-3 tw-flex tw-items-center tw-zoom-in">
                        <div class="tw-w-10 tw-h-10 tw-flex-none tw-image-fit tw-rounded-full tw-overflow-hidden">
                            <div class="circle tw-border tw-border-gray-200 tw-border-solid tw-shadow ub-cover-1-1"
                                 :style="{backgroundImage:`url(${memberUser.avatar||'/asset/image/avatar.svg'})`}"></div>
                        </div>
                        <div class="tw-ml-4 tw-mr-auto">
                            <div class="tw-font-medium">{{ memberUser.username || '' }}</div>
                        </div>
                        <div>
                            <a href="javascript:;" @click="doMemberUserLogout()">退出</a>
                        </div>
                    </div>
                    <div class="tw-p-4">
                        <a class="btn btn-round" href="https://modstart.com/member_ms_module_license" target="_blank">
                            <i class="iconfont icon-lock"></i>
                            已授权的模块
                        </a>
                        <a class="btn btn-round" href="https://modstart.com/developer" target="_blank">
                            <i class="iconfont icon-code"></i>
                            成为模块开发者
                        </a>
                    </div>
                </div>
            </div>
        </el-dialog>
        <el-dialog :visible.sync="commandDialogShow"
                   :show-close="commandDialogFinish"
                   :close-on-press-escape="false"
                   :close-on-click-modal="false"
                   append-to-body>
            <div slot="title">
                <div class="ub-text-bold ub-text-primary">
                    <i class="iconfont icon-code"></i>
                    {{ commandDialogTitle }}
                </div>
            </div>
            <div class="tw-bg-gray-900 tw-font-mono tw-leading-8 tw-p-4 tw-text-white" v-if="commandDialogShow">
                <div v-for="(msg,msgIndex) in commandDialogMsgs" v-html="msg"></div>
                <div v-if="!commandDialogFinish">
                    <i class="iconfont icon-loading tw-inline-block tw-animate-spin"></i>
                    当前操作已运行 {{ commandDialogRunElapse }} s ...
                </div>
                <div v-else>
                    <i class="iconfont icon-check"></i>
                    操作已运行完成
                </div>
            </div>
            <div class="tw-p-4 tw-text-center" v-if="commandDialogFinish">
                <el-button type="danger" @click="commandDialogShow=false">关闭</el-button>
            </div>
        </el-dialog>
        <el-dialog :visible.sync="installVersionDialogShow"
                   :close-on-press-escape="false"
                   :close-on-click-modal="false"
                   append-to-body>
            <div slot="title">
                <div class="ub-text-bold ub-text-primary" v-if="installVersionModule">
                    <i class="iconfont icon-code"></i>
                    安装 {{ installVersionModule.title }} 其他版本
                </div>
            </div>
            <div v-if="installVersionModule">
                <table class="ub-table tw-w-full tw-font-mono">
                    <tbody>
                    <tr v-for="(v,vIndex) in installVersionReleases">
                        <td width="100">v{{ v.version }}</td>
                        <td>
                            <span class="ub-tag warning" v-if="v.status==='preview'">预览</span>
                            {{ v.feature }}
                        </td>
                        <td width="100">{{ v.time }}</td>
                        <td>
                            <a href="javascript:;" @click="doInstallVersionSubmit(installVersionModule,v.version)">
                                <i class="iconfont icon-plus"></i>
                                安装
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import {BeanUtil} from '@ModStartAsset/svue/lib/util'
import {Storage} from '@ModStartAsset/svue/lib/storage'

const UrlWatcher = require('@ModStartAsset/lib/urlWatcher.js');


export default {
    name: "ModuleStore",
    data() {
        return {
            loading: true,
            search: {
                tab: 'store',
                priceType: 'all',
                isRecommend: false,
                categoryId: 0,
                type: '',
                keywords: '',
            },
            commandDialogMsgs: [],
            commandDialogRunStart: 0,
            commandDialogRunElapse: 0,
            commandDialogShow: false,
            commandDialogFinish: false,
            commandDialogTitle: '',
            memberUserShow: false,
            memberUserLoading: false,
            memberLoginCaptchaImage: null,
            memberLoginInfo: {
                username: '',
                password: '',
                captcha: '',
                agree: false,
            },
            storeApiToken: Storage.get('storeApiToken', ''),
            memberUser: {
                id: 0,
                username: '',
                avatar: '',
            },
            categories: [],
            types: [],
            modules: [],
            storeConfig: {
                disable: false,
            },
            installVersionDialogShow: false,
            installVersionReleases: [],
            installVersionModule: null,
            payWatcher: null,
        }
    },
    watch: {
        memberUser: {
            handler(n, o) {
                this.doLoad()
            },
            deep: true
        }
    },
    computed: {
        filterModules() {
            const results = this.modules.filter(module => {
                switch (this.search.tab) {
                    case 'store':
                        if (module._isLocal) return false
                        if (module._isSystem) return false
                        break
                    case 'installed':
                        if (!module._isInstalled) return false
                        if (module._isSystem) return false
                        break;
                    case 'enabled':
                        if (!module._isEnabled) return false
                        if (module._isSystem) return false
                        break;
                    case 'disabled':
                        if (!module._isInstalled || module._isEnabled) return false
                        if (module._isSystem) return false
                        break;
                    case 'local':
                        if (!module._isLocal) return false
                        if (module._isSystem) return false
                        break
                    case 'system':
                        if (!module._isSystem) return false
                        break
                    case 'upgradeable':
                        if (!(module._isInstalled && !module._isLocal && this.canUpgrade(module._localVersion, module.latestVersion))) {
                            return false
                        }
                        if (module._isSystem) return false
                        break
                }
                if (this.search.isRecommend) {
                    if (!module.isRecommend) {
                        return false
                    }
                }
                if (!!this.search.type) {
                    if (!module.types.includes(this.search.type)) {
                        return false
                    }
                }
                switch (this.search.priceType) {
                    case 'free':
                        if (module.isFee) return false
                        break
                    case 'fee':
                        if (!module.isFee) return false
                        break
                }
                if (this.search.categoryId) {
                    if (module.categoryId !== this.search.categoryId) return false
                }
                if (this.search.keywords) {
                    if (module.title.toLowerCase().includes(this.search.keywords.toLowerCase())) {
                        return true
                    }
                    if (module.name.toLowerCase().includes(this.search.keywords.toLowerCase())) {
                        return true
                    }
                    if (module.description.toLowerCase().includes(this.search.keywords.toLowerCase())) {
                        return true
                    }
                    return false
                }
                return true
            })
            return results
        }
    },
    mounted() {
        this.doLoad()
        this.doLoadStore()
        setInterval(() => {
            this.commandDialogRunElapse = parseInt(((new Date()).getTime() - this.commandDialogRunStart) / 1000)
        }, 1000)
    },
    methods: {
        versionCompare(left, right) {
            let a = left.split('.'), b = right.split('.')
            for (let i = 0, len = Math.max(a.length, b.length); i < len; i++) {
                if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
                    return 1;
                } else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
                    return -1;
                }
            }
            return 0;
        },
        canUpgrade(currentVersion, latestVersion) {
            return this.versionCompare(currentVersion, latestVersion) < 0
        },
        doStoreRequest(url, data, cbSuccess, cbError) {
            const cbErrorDefault = (res) => {
                this.$dialog.tipError(res.msg)
            }
            if (!cbError) {
                cbError = cbErrorDefault
            }
            $.ajax({
                url: `${window.__data.apiBase}/api/${url}`,
                dataType: 'jsonp',
                timeout: 10 * 1000,
                data: Object.assign(data, {
                    api_token: this.storeApiToken,
                    modstartParam: JSON.stringify(window.__data.modstartParam),
                }),
                success: (res) => {
                    if (res.code) {
                        if (res.code === 1002) {
                            this.doMemberLoginCaptchaRefresh()
                        }
                        if (true !== cbError(res)) {
                            cbErrorDefault(res)
                        }
                    } else {
                        cbSuccess(res)
                    }
                },
                error: (res) => {
                    if (true !== cbError({code: -1, msg: '请求出现错误'})) {
                        cbErrorDefault({code: -1, msg: '请求出现错误'})
                    }
                },
                jsonp: 'callback',
            });
        },
        doMemberUserLogout() {
            this.$dialog.confirm('确认退出？', () => {
                this.storeApiToken = ''
                Storage.set('storeApiToken', '')
                this.memberUserShow = false
                this.doLoadStore()
            })
        },
        doSubmitCheck(e) {
            if (e.keyCode === 13) {
                this.doMemberLoginSubmit()
            }
        },
        doMemberLoginSubmit() {
            if (!this.memberLoginInfo.username || !this.memberLoginInfo.password || !this.memberLoginInfo.captcha) {
                return
            }
            if (!this.memberLoginInfo.agree) {
                this.$dialog.tipError('请先同意使用协议')
                return
            }
            this.$dialog.loadingOn()
            this.doStoreRequest('store/login', this.memberLoginInfo, res => {
                this.$dialog.loadingOff()
                this.$dialog.tipSuccess('登录成功')
                this.doLoadStoreMember()
                this.memberLoginInfo.username = ''
                this.memberLoginInfo.password = ''
                this.memberLoginInfo.captcha = ''
                this.memberUserShow = false
            }, res => {
                this.$dialog.loadingOff()
            })
        },
        doMemberLoginCaptchaRefresh(cb) {
            this.doStoreRequest('store/login_captcha', {}, res => {
                this.memberLoginCaptchaImage = res.data.image
                cb && cb()
            })
        },
        doMemberLoginShow() {
            if (this.memberUser.id > 0) {
                this.memberUserShow = true
            } else {
                this.$dialog.loadingOn()
                this.doMemberLoginCaptchaRefresh(() => {
                    this.$dialog.loadingOff()
                    this.memberUserShow = true
                })
            }
        },
        doLoadStoreMember() {
            this.memberUserLoading = true
            this.doStoreRequest('store/member', {}, res => {
                this.memberUserLoading = false
                BeanUtil.update(this.memberUser, res.data)
            }, res => {
                this.memberUserLoading = false
            })
        },
        doLoadStore() {
            if (!!this.storeApiToken) {
                this.doLoadStoreMember()
            } else {
                this.doStoreRequest('store/config', {}, res => {
                    this.storeApiToken = res.data.apiToken
                    Storage.set('storeApiToken', res.data.apiToken)
                    this.doLoadStoreMember()
                })
            }
        },
        doLoad() {
            this.$api.post(this.$url.admin('module_store/all'), {
                memberUserId: this.memberUser.id,
                apiToken: this.storeApiToken,
            }, res => {
                this.loading = false
                this.categories = res.data.categories
                this.types = res.data.types
                this.modules = res.data.modules
                this.storeConfig = res.data.storeConfig
            })
        },
        commandDialogMsgsPush(msg) {
            if (!msg) {
                return
            }
            if (!Array.isArray(msg)) {
                msg = [msg]
            }
            msg = msg.map(m => {
                m = m.trim()
                if (!m.startsWith('<')) {
                    m = '<i class="iconfont icon-hr"></i> ' + m
                }
                return m
            })
            this.commandDialogMsgs = this.commandDialogMsgs.concat(msg)
        },
        doCommand(command, data, step, title) {
            step = step || null
            title = title || null
            if (null === step) {
                this.commandDialogMsgs = []
                this.commandDialogShow = true
                this.commandDialogFinish = false
            }
            if (title) {
                this.commandDialogTitle = title
                this.commandDialogMsgsPush(title)
            }
            this.commandDialogRunStart = (new Date()).getTime()
            this.commandDialogRunElapse = 0
            this.$api.post(this.$url.admin(`module_store/${command}`), {
                token: this.storeApiToken,
                step: step,
                data: JSON.stringify(data)
            }, res => {
                this.commandDialogMsgsPush(res.data.msg)
                if (res.data.finish) {
                    this.commandDialogFinish = true
                    this.doLoad()
                    return
                } else {
                    setTimeout(() => {
                        this.doCommand(res.data.command, res.data.data, res.data.step)
                    }, 1000)
                }
            }, res => {
                this.commandDialogMsgsPush('<i class="iconfont icon-close ub-text-danger"></i> <span class="ub-text-danger">' + res.msg + '</span>')
                if (res.data && res.data.msg) {
                    this.commandDialogMsgsPush(res.data.msg)
                }
                if (res.data && res.data.payWatchUrl) {
                    this.startPayWatch(res.data.buyCodeId, res.data.payWatchUrl)
                }
                this.commandDialogFinish = true
                return true
            })
        },
        startPayWatch(buyCodeId, payWatchUrl) {
            const buyCodeVisible = () => {
                return ($('[data-buy-code=' + buyCodeId + ']').length > 0)
            }
            this.$nextTick(() => {
                if (this.urlWatcher) {
                    this.urlWatcher.stop()
                }
                if (!buyCodeVisible()) {
                    return;
                }
                this.urlWatcher = new UrlWatcher({
                    url: payWatchUrl,
                    jsonp: true,
                    data: {},
                    maxRound: 100,
                    requestFinish: (res) => {
                        if (!buyCodeVisible()) {
                            this.urlWatcher.stop()
                            return;
                        }
                        MS.api.defaultCallback(res, {
                            success: (res) => {
                                switch (res.data.status) {
                                    case 'Payed':
                                        this.$dialog.alertSuccess('支付成功，请关闭弹窗重新安装', () => {
                                            this.commandDialogShow = false
                                        })
                                        break;
                                    case 'WaitPay':
                                        this.urlWatcher.next();
                                        break;
                                }
                            }
                        });
                    },
                    expired: () => {
                        this.$dialog.alertError('支付超时，请关闭弹窗重新请求支付二维码', () => {
                            this.commandDialogShow = false
                        })
                    },
                })
                this.urlWatcher.start()
            })
        },
        doInstallVersion(module) {
            this.$dialog.loadingOn()
            this.doStoreRequest('store/module_releases', {module: module.name}, res => {
                this.$dialog.loadingOff()
                this.installVersionModule = module
                this.installVersionReleases = res.data.releases
                this.installVersionDialogShow = true
            }, res => {
                this.$dialog.loadingOff()
            })
        },
        doInstall(module) {
            if (!this.memberUser.id) {
                this.doMemberLoginShow()
                return
            }
            this.doCommand('install', {
                module: module.name,
                version: module.latestVersion,
                isLocal: module._isLocal
            }, null, `安装模块 ${module.title}（${module.name}） V${module.latestVersion}`)
        },
        doInstallVersionSubmit(module, version) {
            if (!this.memberUser.id) {
                this.doMemberLoginShow()
                return
            }
            this.doCommand('install', {
                module: module.name,
                version: version,
                isLocal: module._isLocal
            }, null, `安装模块 ${module.title}（${module.name}） V${version}`)
        },
        doDisable(module) {
            this.doCommand('disable', {
                module: module.name,
                version: module._localVersion
            }, null, `禁用模块 ${module.title}（${module.name}）`)
        },
        doEnable(module) {
            this.doCommand('enable', {
                module: module.name,
                version: module._localVersion
            }, null, `启用模块 ${module.title}（${module.name}）`)
        },
        doUninstall(module) {
            this.$dialog.confirm('确认卸载？', () => {
                this.doCommand('uninstall', {
                    module: module.name,
                    version: module._localVersion,
                    isLocal: module._isLocal
                }, null, `卸载模块 ${module.title}（${module.name}）`)
            })
        },
        doUpgrade(module) {
            if (!this.memberUser.id) {
                this.doMemberLoginShow()
                return
            }
            this.$dialog.confirm('确认升级？', () => {
                this.doCommand('upgrade', {
                    module: module.name,
                    version: module.latestVersion,
                }, null, `升级模块 ${module.title}（${module.name}） V${module.latestVersion}`)
            })
        },
        doConfig(module) {
            this.$dialog.dialog(this.$url.admin(`module_store/config/${module.name}`))
        },
        doScanLogin() {
            if (!this.memberLoginInfo.agree) {
                this.$dialog.tipError('请先同意相关协议')
                return
            }
            this.$dialog.loadingOn()
            this.doStoreRequest('store/login_wechatmp_qrcode', {}, res => {
                this.$dialog.loadingOff()
                let isOpen = false
                let dialog = null
                const checkLogin = () => {
                    if (!isOpen) {
                        return
                    }
                    this.doStoreRequest('store/login_wechatmp_info', {}, res => {
                        if (res.data.memberUserId) {
                            this.doLoadStoreMember()
                            this.$dialog.dialogClose(dialog)
                            this.$dialog.tipSuccess('登录成功')
                        } else if (res.data.oauthUserInfo) {
                            this.$dialog.dialogClose(dialog)
                            this.$dialog.alertError('当前微信未绑定账号，请先注册')
                        } else {
                            setTimeout(() => {
                                checkLogin()
                            }, 3000)
                        }
                    })
                }
                checkLogin();
                dialog = this.$dialog.dialogContent(`<img style="width:200px;height:200px;" src="${res.data.qrcode}" />`, {
                    openCallback: () => {
                        isOpen = true
                        setTimeout(() => {
                            checkLogin()
                        }, 3000)
                    },
                    closeCallback: () => {
                        isOpen = false
                    },
                })
            }, res => {
                this.$dialog.loadingOff()
            })
        }
    }
}
</script>

<style lang="less">
.pb-member-info-dialog {
    max-width: 18rem;
}
</style>
