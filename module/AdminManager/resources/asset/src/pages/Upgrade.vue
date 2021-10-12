<template>
    <div>
        <div v-loading="loading">
            <div v-if="!memberUser.id" class="ub-alert ub-alert-warning">
                <i class="iconfont icon-warning"></i>
                您还没有登录，登录后才能进行升级操作
                <a href="javascript:;" @click="doMemberLoginShow()"><i class="iconfont icon-user"></i>立即登录</a>
            </div>
        </div>
        <div class="ub-panel">
            <div class="head">
                <div class="more" v-if="memberUser.id">
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
                                {{memberUser.username}}
                            </span>
                        <span v-else>
                                <i class="iconfont icon-user"></i>
                                未登录
                            </span>
                    </el-button>
                </div>
                <div class="title">
                    <i class="iconfont icon-cog"></i>
                    系统升级
                </div>
            </div>
            <div class="body">
                <div>
                    <div class="tw-py-2">
                        当前版本：<code>v{{info.version || '-'}}</code>
                    </div>
                    <div class="tw-py-2">
                        最新版本：<code>v{{info.latestVersion || '-'}}</code>
                        <span class="ub-text-warning tw-ml-4" v-if="!info.autoUpgrade && info.version">
                            缺少自动升级包 v{{info.version || '-'}} <i class="iconfont icon-direction-right"></i> v{{info.latestVersion || '-'}}，
                            请 <a href="https://modstart.com/download" target="_blank">手动下载</a> 最新安装包升级
                        </span>
                        <a href="javascript:;" class="ub-text-danger tw-pl-4"
                           v-if="info.autoUpgrade&&!upgradeDetailShow"
                           @click="upgradeDetailShow=true">
                            <i class="iconfont icon-up"></i> 立即升级
                        </a>
                    </div>
                    <div class="tw-mt-4 tw-bg-gray-800 tw-text-white tw-rounded tw-p-4"
                         v-if="upgradeDetailShow && info.autoUpgrade">
                        <div class="tw-py-2">
                            <i class="iconfont icon-right"></i>
                            系统即将升级到
                            v{{info.autoUpgrade.version}}（增加文件{{info.autoUpgrade.addCount}}，更新文件{{info.autoUpgrade.updateCount}}个，删除文件{{info.autoUpgrade.deleteCount}}个），请您确定已经完成系统的备份（文件和数据库）
                        </div>
                        <div class="tw-mt-2" v-if="">
                            <a href="javascript:;" class="btn btn-danger" @click="doUpgradeSubmit" v-if="!upgradeRunning">
                                我已知道风险，立即升级
                            </a>
                        </div>
                        <div class="tw-bg-gray-900 tw-font-mono tw-leading-8 tw-p-4 tw-text-white"
                             v-if="upgradeRunning">
                            <div v-for="(msg,msgIndex) in upgradeMsgs" v-html="msg"></div>
                            <div v-if="!upgradeFinish">
                                <i class="iconfont icon-loading tw-inline-block tw-animate-spin"></i>
                                当前操作已运行 {{upgradeRunElapse}} s ...
                            </div>
                            <div v-else>
                                <i class="iconfont icon-check"></i>
                                操作已运行完成
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
                                还没有账号？<a href="https://modstart.com" target="_blank">立即注册</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>
                <div>
                    <div class="tw-bg-white tw-rounded-sm tw-mb-2 tw-box tw-px-5 tw-py-3 tw-mb-3 tw-flex tw-items-center tw-zoom-in">
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
    </div>
</template>

<script>
    import {BeanUtil} from '@ModStartAsset/svue/lib/util'
    import {Storage} from '@ModStartAsset/svue/lib/storage'

    export default {
        name: "Upgrade",
        data() {
            return {
                loading: true,
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

                infoLoading: true,
                info: {
                    version: null,
                    latestVersion: null,
                    autoUpgrade: null,
                },
                upgradeDetailShow: false,

                upgradeRunning: false,
                upgradeRunStart: 0,
                upgradeRunElapse: 0,
                upgradeMsgs: [],
                upgradeFinish: false,

            }
        },
        mounted() {
            this.$api.post(this.$url.admin('upgrade/info'), {}, res => {
                this.info = Object.assign(this.info, res.data)
                this.infoLoading = false
            })
            setInterval(() => {
                this.upgradeRunElapse = parseInt(((new Date()).getTime() - this.upgradeRunStart) / 1000)
            }, 1000)
            this.doLoadStore()
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
            doLoadStore() {
                this.loading = false
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
            doMemberUserLogout() {
                this.$dialog.confirm('确认退出？', () => {
                    this.storeApiToken = ''
                    Storage.set('storeApiToken', '')
                    this.memberUserShow = false
                    this.doLoadStore()
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
            doSubmitCheck(e) {
                if (e.keyCode === 13) {
                    this.doMemberLoginSubmit()
                }
            },
            doMemberLoginSubmit() {
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
            doUpgradeSubmit() {
                if (this.memberUser.id > 0) {
                    this.doCommand({
                        toVersion: this.info.autoUpgrade.version,
                    }, null, `系统升级到 V${this.info.autoUpgrade.version}`)
                } else {
                    this.$dialog.loadingOn()
                    this.doMemberLoginCaptchaRefresh(() => {
                        this.$dialog.loadingOff()
                        this.memberUserShow = true
                    })
                }
            },
            doCommand(data, step, title) {
                step = step || null
                title = title || null
                if (null === step) {
                    this.upgradeMsgs = []
                    this.upgradeFinish = false
                }
                if (title) {
                    this.upgradeMsgs.push('<i class="iconfont icon-hr"></i> ' + title)
                }
                this.upgradeRunning = true
                this.upgradeRunStart = (new Date()).getTime()
                this.upgradeRunElapse = 0
                this.$api.post(this.$url.admin(`upgrade`), {
                    token: this.storeApiToken,
                    step: step,
                    data: JSON.stringify(data)
                }, res => {
                    if (Array.isArray(res.data.msg)) {
                        this.upgradeMsgs = this.upgradeMsgs.concat(res.data.msg)
                    } else {
                        this.upgradeMsgs.push(res.data.msg)
                    }
                    if (res.data.finish) {
                        this.upgradeFinish = true
                    } else {
                        setTimeout(() => {
                            this.doCommand(res.data.data, res.data.step)
                        }, 1000)
                    }
                }, res => {
                    this.upgradeMsgs.push('<i class="iconfont icon-close ub-text-danger"></i> <span class="ub-text-danger">' + res.msg + '</span>')
                    this.upgradeFinish = true
                    return true
                })
            },
        }
    }
</script>

