<template>
    <div>
        <div class="ub-alert ub-alert-warning">
            <i class="iconfont icon-warning"></i>
            应用可在线安装、卸载、禁用、启用、配置、升级插件，插件升级前请做好备份。
        </div>
        <div class="tw-bg-white tw-rounded">
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
                <el-tab-pane name="local">
                    <span slot="label">
                        <i class="iconfont icon-pc"></i> 本地所有模块
                    </span>
                </el-tab-pane>
            </el-tabs>
            <div class="ub-padding">
                <div class="tw-float-right">
                    <el-button style="padding:0.25rem;" :loading="memberUserLoading" @click="doMemberLoginShow()">
                        <span v-if="memberUserLoading">
                            检测中
                        </span>
                        <span v-else-if="memberUser.id>0">
                            <div v-if="memberUser.avatar" class="ub-cover-1-1 tw-rounded-full"
                                 style="width:0.8rem;display:inline-block;vertical-align:middle;"
                                 :style="{backgroundImage:`url(${memberUser.avatar})`}"></div>
                            <i v-else class="iconfont icon-user"></i>
                            {{memberUser.username}}
                        </span>
                        <span v-else>
                            <i class="iconfont icon-user"></i>
                            未登录
                        </span>
                    </el-button>
                </div>
                <el-radio-group v-model="search.type">
                    <el-radio-button label="all"><i class="iconfont icon-list-alt"></i> 全部</el-radio-button>
                    <el-radio-button label="free"><i class="iconfont icon-gift"></i> 免费</el-radio-button>
                    <el-radio-button label="fee"><i class="iconfont icon-cny"></i> 付费</el-radio-button>
                </el-radio-group>
                <el-select v-model="search.categoryId" placeholder="请选择" style="width:auto;">
                    <el-option :key="0" label="全部" :value="0"></el-option>
                    <el-option
                            v-for="item in categories"
                            :key="item.id"
                            :label="item.title"
                            :value="item.id">
                    </el-option>
                </el-select>
                <el-input prefix-icon="el-icon-search"
                          v-model="search.keywords"
                          style="width:10rem;"
                          placeholder="搜索模块"></el-input>
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
                        <div class="tw-bg-white tw-p-2 tw-rounded tw-mb-2 tw-border-gray-200 tw-border-solid tw-border tw-shadow">
                            <div style="padding-left:6rem;">
                                <div class="tw-w-28 tw-float-left" style="margin-left:-6rem;">
                                    <a v-if="module.url"
                                       :href="module.url"
                                       class="ub-cover-3-2 tw-shadow tw-w-28 tw-rounded"
                                       :style="{'background-image':'url('+module.cover+')'}"></a>
                                    <div v-else
                                         class="tw-shadow tw-w-28 tw-rounded ub-text-center tw-text-gray-300">
                                        <i class="iconfont icon-category"
                                           style="font-size:1rem;line-height:3.733333rem;"></i>
                                    </div>
                                </div>
                                <div>
                                    <a :href="module.url" target="_blank"
                                       class="tw-font-bold tw-text-gray-700 ub-text-truncate tw-block">
                                        <span v-if="module._isLocal" class="ub-tag primary sm ub-bg-a">本地插件</span>
                                        <span class="pb-search-keywords">{{module.title}}</span>
                                    </a>
                                    <div>
                                    <span v-if="!module.isFee && !module._isLocal"
                                          class="ub-text-success">免费</span>
                                        <div v-if="module.isFee" class="ub-text-danger">
                                            <span v-if="module.priceYearEnable">￥{{module.priceYear}}</span>
                                            <span v-else-if="module.priceSuperEnable">￥{{module.priceSuper}}</span>
                                        </div>
                                    </div>
                                    <div class="tw-text-gray-400 tw-text-sm tw-mt-2 pb-search-keywords" style="height:2rem;overflow:auto;">
                                        {{module.description}}
                                    </div>
                                </div>
                            </div>
                            <div v-if="!module._isSystem"
                                 class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-100 tw-mt-2 tw-pt-2 tw-text-gray-500">
                                <div class="tw-float-right" v-if="module._isInstalled">
                                    <a v-if="module._isInstalled && module._hasConfig" href="javascript:;"
                                       @click="doConfig(module)">
                                        <i class="iconfont icon-cog"></i> 配置
                                    </a>
                                </div>
                                <a v-if="!module._isInstalled" href="javascript:;" @click="doInstall(module)"
                                   class="tw-mr-4"
                                >
                                    <i class="iconfont icon-plus"></i> 安装
                                </a>
                                <a v-if="module._isInstalled && !module._isLocal && module.latestVersion!==module._localVersion"
                                   @click="doUpgrade(module)"
                                   href="javascript:;" class="ub-text-warning tw-mr-4">
                                    <i class="iconfont icon-direction-up"></i> 升级
                                </a>
                                <a v-if="module._isInstalled && module._isEnabled" href="javascript:;"
                                   @click="doDisable(module)"
                                   class="ub-text-danger tw-mr-4">
                                    <i class="iconfont icon-pause"></i> 禁用
                                </a>
                                <a v-if="module._isInstalled && !module._isEnabled" href="javascript:;"
                                   class="tw-mr-4"
                                   @click="doEnable(module)"
                                >
                                    <i class="iconfont icon-play"></i> 启用
                                </a>
                                <a v-if="module._isInstalled && !module._isEnabled" href="javascript:;"
                                   @click="doUninstall(module)"
                                   class="ub-text-danger tw-mr-4">
                                    <i class="iconfont icon-trash"></i> 卸载
                                </a>
                            </div>
                            <div v-else
                                 class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-100 tw-mt-2 tw-pt-2 tw-text-gray-500">
                                <div class="ub-text-muted">
                                    系统模块不能动态配置
                                </div>
                            </div>
                            <div class="tw-border-0 tw-border-solid tw-border-t tw-border-gray-100 tw-mt-2 tw-pt-2 tw-text-gray-500">
                                <div class="ub-text-muted" v-if="module._isLocal">
                                    标识：<span class="pb-search-keywords">{{module.name}}</span>，
                                    版本 V{{module._localVersion}}
                                </div>
                                <div class="ub-text-muted" v-else-if="module._isInstalled">
                                    标识：<span class="pb-search-keywords">{{module.name}}</span>，
                                    已安装 V{{module._localVersion}} 版，最新版 V{{module.latestVersion}}
                                </div>
                                <div class="ub-text-muted" v-else>
                                    标识：<span class="pb-search-keywords">{{module.name}}</span>，
                                    最新版 V{{module.latestVersion}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog :visible.sync="memberUserShow" append-to-body>
            <div slot="title">
                <i class="iconfont icon-user"></i>
                我的信息
            </div>
            <div v-if="!memberUser.id">
                <div style="max-width:300px;margin:0 auto 2rem auto;">
                    <div class="tw-font-bold tw-py-2 tw-text-center tw-text-lg ub-text-primary">
                        <i class="iconfont icon-user"></i>
                        登录ModStart账号
                    </div>
                    <div class="ub-form vertical">
                        <div class="line">
                            <div class="label">用户名</div>
                            <div class="field">
                                <input type="text" class="form" v-model="memberLoginInfo.username" placeholder="输入用户名"/>
                            </div>
                        </div>
                        <div class="line">
                            <div class="label">密码</div>
                            <div class="field">
                                <input type="password" class="form" v-model="memberLoginInfo.password"
                                       placeholder="输入密码"/>
                            </div>
                        </div>
                        <div class="line">
                            <div class="label">验证码</div>
                            <div class="field">
                                <div class="row no-gutters">
                                    <div class="col-6">
                                        <input type="text" class="form" v-model="memberLoginInfo.captcha"
                                               autocomplete="off"
                                               placeholder="图片验证码"/>
                                    </div>
                                    <div class="col-6">
                                        <img class="captcha" title="刷新验证" :src="memberLoginCaptchaImage"
                                             @click="doMemberLoginCaptchaRefresh()"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="line">
                            <div class="field">
                                <button type="submit" class="btn btn-primary btn-block" @click="doMemberLoginSubmit()">
                                    登录
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div>
                    <div class="tw-bg-white tw-rounded-sm tw-mb-2 tw-box tw-px-5 tw-py-3 tw-mb-3 tw-flex tw-items-center tw-zoom-in"
                         data-repeat="3">
                        <div class="tw-w-10 tw-h-10 tw-flex-none tw-image-fit tw-rounded-full tw-overflow-hidden">
                            <div class="circle tw-border tw-border-gray-200 tw-border-solid tw-shadow ub-cover-1-1"
                                 :style="{backgroundImage:`url(${memberUser.avatar||'/asset/image/avatar.png'})`}"></div>
                        </div>
                        <div class="tw-ml-4 tw-mr-auto">
                            <div class="tw-font-medium">{{memberUser.username || ''}}</div>
                        </div>
                        <div>
                            <a href="javascript:;" @click="doMemberUserLogout()">退出</a>
                        </div>
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
                    {{commandDialogTitle}}
                </div>
            </div>
            <div class="tw-bg-gray-900 tw-font-mono tw-leading-8 tw-p-4 tw-text-white">
                <div v-for="(msg,msgIndex) in commandDialogMsgs" v-html="msg"></div>
                <div v-if="!commandDialogFinish">
                    <i class="iconfont icon-loading tw-inline-block tw-animate-spin"></i>
                    当前操作已运行 {{commandDialogRunElapse}} s ...
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
    </div>
</template>

<script>
    import {BeanUtil} from '@ModStartAsset/svue/lib/util'
    import {Storage} from '@ModStartAsset/svue/lib/storage'

    export default {
        name: "ModuleStore",
        data() {
            return {
                loading: true,
                search: {
                    tab: 'store',
                    type: 'all',
                    categoryId: 0,
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
                },
                storeApiToken: Storage.get('storeApiToken', ''),
                memberUser: {
                    id: 0,
                    username: '',
                    avatar: '',
                },
                categories: [],
                modules: [],
            }
        },
        computed: {
            filterModules() {
                const results = this.modules.filter(module => {
                    switch (this.search.tab) {
                        case 'store':
                            if (module._isLocal) return false
                            break
                        case 'local':
                            if (!module._isLocal) return false
                            break
                        case 'installed':
                            if (!module._isInstalled) return false
                            break;
                        case 'enabled':
                            if (!module._isEnabled) return false
                            break;
                    }
                    switch (this.search.type) {
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
                if (this.search.keywords) {
                    this.$nextTick(() => {
                        $('.pb-search-keywords').unmark();
                        $('.pb-search-keywords').mark(this.search.keywords, {});
                    })
                }
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
            doStoreRequest(url, data, cbSuccess, cbError) {
                const cbErrorDefault = (res) => {
                    this.$dialog.tipError(res.msg)
                }
                if (!cbError) {
                    cbError = cbErrorDefault
                }
                $.ajax({
                    // url: `http://org.demo.soft.host/api/${url}`,
                    url: `https://modstart.com/api/${url}`,
                    dataType: 'jsonp',
                    data: Object.assign(data, {api_token: this.storeApiToken}),
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
                    this.doLoadStoreMember()
                    this.memberUserShow = false
                })
            },
            doMemberLoginSubmit() {
                this.$dialog.loadingOn()
                this.doStoreRequest('store/login', this.memberLoginInfo, res => {
                    this.$dialog.loadingOff()
                    this.$dialog.tipSuccess('登录成功')
                    this.doLoadStoreMember()
                    this.memberUserShow = false
                }, res => {
                    this.$dialog.loadingOff()
                })
            },
            doMemberLoginCaptchaRefresh(cb) {
                this.doStoreRequest('store/login_captcha', {}, res => {
                    this.memberLoginCaptchaImage = res.data.image
                    cb()
                })
            },
            doMemberLoginShow() {
                if (this.memberUser.id > 0) {
                    this.memberUserShow = true
                } else {
                    this.doMemberLoginCaptchaRefresh(() => {
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
                this.$api.post(this.$url.admin('module_store/all'), {}, res => {
                    this.loading = false
                    this.categories = res.data.categories
                    this.modules = res.data.modules
                })
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
                    this.commandDialogMsgs.push('<i class="iconfont icon-hr"></i> ' + title)
                }
                this.commandDialogRunStart = (new Date()).getTime()
                this.commandDialogRunElapse = 0
                this.$api.post(this.$url.admin(`module_store/${command}`), {
                    token: this.storeApiToken,
                    step: step,
                    data: JSON.stringify(data)
                }, res => {
                    if (Array.isArray(res.data.msg)) {
                        this.commandDialogMsgs = this.commandDialogMsgs.concat(res.data.msg)
                    } else {
                        this.commandDialogMsgs.push(res.data.msg)
                    }
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
                    this.commandDialogMsgs.push('<i class="iconfont icon-close ub-text-danger"></i> <span class="ub-text-danger">' + res.msg + '</span>')
                    this.commandDialogFinish = true
                    return true
                })
            },
            doInstall(module) {
                this.doCommand('install', {
                    module: module.name,
                    version: module.latestVersion,
                    isLocal: module._isLocal
                }, null, `安装模块 ${module.title}（${module.name}） V${module.latestVersion}`)
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
                this.$dialog.confirm('确认升级？', () => {
                    this.doCommand('upgrade', {
                        module: module.name,
                        version: module.latestVersion,
                    }, null, `升级模块 ${module.title}（${module.name}） V${module.latestVersion}`)
                })
            },
            doConfig(module) {
                this.$dialog.dialog(this.$url.admin(`module_store/config/${module.name}`))
            }
        }
    }
</script>

