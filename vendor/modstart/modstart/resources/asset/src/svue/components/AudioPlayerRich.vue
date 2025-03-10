<template>
    <div>
        <div class="tw-border tw-border-gray-100 tw-border-solid tw-rounded-lg tw-py-2">
            <pre v-if="false" style="white-space:wrap;font-size:10px;">{{ JSON.stringify(debugInfo, null, 2) }}</pre>
            <div class="tw-px-2 tw-overflow-hidden"
                 :style="((isRecording||isTrimming||(showWave&&!recordVisible)) && waveVisible) ? 'height:40px;' : 'height:0;'">
                <div ref="waveContainer" style="height:40px;" class="w-full overflow-hidden"></div>
            </div>
            <div v-if="!recordVisible&&waveUrl" class="tw-h-10 tw-px-2 tw-flex tw-items-center">
                <div>
                    <div v-if="!isPlaying"
                         @click="doPlay" class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                        <i class="el-icon-video-play tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                    </div>
                    <div v-if="isPlaying"
                         @click="doPause" class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                        <i class="el-icon-video-pause tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                    </div>
                </div>
                <div class="tw-ml-3 tw-text-gray-500 tw-w-24 tw-text-sm tw-font-mono">
                    {{ timeCurrentFormat + '/' + timeTotalFormat }}
                </div>
                <div class="tw-ml-3 tw-flex-grow">
                    <el-slider :value="timeCurrent"
                               :max="timeTotal"
                               @input="onSliderInput"
                               @change="onSeek"
                               :show-tooltip="false"
                               :step="0.001"
                               :min="0"/>
                </div>
                <div class="tw-ml-3">
                    <el-tooltip :content="'收起'"
                                v-if="showWave&&waveVisible&&!isTrimming&&!isRecording">
                        <div @click="waveVisible=false"
                             class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                            <i class="el-icon-arrow-up tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                        </div>
                    </el-tooltip>
                    <el-tooltip :content="'重新录制'"
                                v-if="recordEnable&&recordUrl&&!isTrimming">
                        <div @click="doRecordClean" class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                            <i class="el-icon-refresh-right tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                        </div>
                    </el-tooltip>
                    <el-tooltip :content="'裁剪音频'"
                                v-if="!isTrimming && trimEnable">
                        <div @click="doTrim"
                             class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                            <i class="el-icon-scissors tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                        </div>
                    </el-tooltip>
                    <el-tooltip :content="'确定裁剪'"
                                v-if="isTrimming && trimEnable">
                        <div @click="doTrimSave"
                             class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                            <i class="el-icon-circle-check tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                        </div>
                    </el-tooltip>
                    <el-tooltip :content="'下载音频'"
                                v-if="!isTrimming && downloadEnable">
                        <div @click="doDownload"
                             class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                            <i class="el-icon-download tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                        </div>
                    </el-tooltip>
                    <el-tooltip :content="'录制音频'"
                                v-if="recordEnable && !isTrimming && !recordUrl">
                        <div @click="doRecord"
                             class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                            <i class="el-icon-microphone tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                        </div>
                    </el-tooltip>
                    <el-tooltip :content="'重新选择'"
                                v-if="showSelectFile&&hasValue">
                        <div class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                            <FileSelectButton @change="onSelectFile"
                                              :extensions="['wav','mp3']">
                                <i class="el-icon-upload2 tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                            </FileSelectButton>
                        </div>
                    </el-tooltip>
                </div>
            </div>
            <div v-if="recordEnable&&recordVisible" class="tw-h-10 tw-px-2 tw-flex tw-items-center">
                <div>
                    <div v-if="!isRecording && waveUrl" @click="doRecordBack"
                         class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                        <i class="el-icon-back tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                    </div>
                    <div v-if="recordInputDevices.length && !isRecording"
                         @click="doRecordStart" class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                        <i class="el-icon-microphone tw-m-auto tw-text-red-700 hover:tw-text-primary tw-text-2xl"></i>
                    </div>
                    <div v-else-if="recordInputDevices.length"
                         @click="doRecordStop"
                         class="tw-cursor-pointer tw-w-8 tw-h-8 tw-inline-flex">
                        <i class="el-icon-finished tw-m-auto tw-text-gray-700 hover:tw-text-primary tw-text-2xl"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <div v-if="!recordInputDevices.length"
                         class="tw-text-sm tw-bg-gray-100 tw-h-10 tw-leading-10 tw-rounded-lg tw-px-5">
                        <i class="el-icon-warning-outline"></i>
                        {{ '未检测到录音设备' }}
                    </div>
                    <el-select v-else v-model="recordInputDeviceSelect" size="mini" style="width: 100%">
                        <el-option v-for="device in recordInputDevices" :key="device.id" :value="device.id">
                            {{ device.name }}
                        </el-option>
                    </el-select>
                </div>
            </div>
            <div v-if="showSelectFile&&!hasValue">
                <FileSelectButton @change="onSelectFile"
                                  :extensions="['wav','mp3']">
                    <div>
                        <i class="el-icon-upload2"></i>
                        选择 mp3/wav 文件
                    </div>
                </FileSelectButton>
            </div>
        </div>
    </div>
</template>

<script>
import WaveSurfer from 'wavesurfer.js';
import RegionsPlugin from 'wavesurfer.js/dist/plugins/regions.esm.js';
import RecordPlugin from 'wavesurfer.js/dist/plugins/record.esm.js';
import {TimeUtil} from "../../lib/time";
import {Dialog} from "../../lib/dialog";
import {AudioUtil} from "../../lib/audio";
import FileSelectButton from "./FileSelectButton.vue";

export default {
    components: {FileSelectButton},
    props: {
        url: {
            type: String,
            default: '',
        },
        recordEnable: {
            type: Boolean,
            default: false,
        },
        trimEnable: {
            type: Boolean,
            default: false,
        },
        downloadEnable: {
            type: Boolean,
            default: false,
        },
        showWave: {
            type: Boolean,
            default: false,
        },
        showSelectFile: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            wave: null,
            waveContainer: null,
            waveUrl: null,
            waveUrlSource: null,
            waveVisible: false,
            waveLoadAutoPlay: false,
            waveIsLoaded: false,
            waveRecord: null,
            trimUrl: null,
            isPlaying: false,
            isTrimming: false,
            recordUrl: null,
            isRecording: false,
            recordInputDeviceSelect: null,
            recordInputDevices: [],
            recordVisible: false,
            timeTotal: 0,
            timeCurrent: 0,
            regions: RegionsPlugin.create(),
        };
    },
    computed: {
        hasValue() {
            return this.url || this.trimUrl || this.recordUrl
        },
        timeTotalSecond() {
            return Math.round(this.timeTotal);
        },
        timeCurrentSecond() {
            return Math.round(this.timeCurrent);
        },
        timeTotalFormat() {
            return TimeUtil.secondsToTime(this.timeTotalSecond);
        },
        timeCurrentFormat() {
            return TimeUtil.secondsToTime(this.timeCurrentSecond);
        },
        isAudioEmpty() {
            return !this.url && !this.trimUrl && !this.recordUrl;
        },
        debugInfo() {
            return {
                waveUrl: this.waveUrl,
                waveUrlSource: this.waveUrlSource,
                waveIsLoaded: this.waveIsLoaded,
            };
        },
    },
    mounted() {
        this.wave = WaveSurfer.create({
            container: this.$refs.waveContainer,
            waveColor: "#4A90E2",
            progressColor: "#FF5733",
            cursorColor: "#333",
            barWidth: 2,
            height: 40,
            plugins: [this.regions],
            autoplay: false,
            cursorWidth: 0,
            sampleRate: 16000,
        });
        this.waveRecord = this.wave.registerPlugin(RecordPlugin.create({
            scrollingWaveform: false,
            renderRecordedAudio: false,
        }));

        this.waveRecord.on('record-end', (blob) => {
            this.recordUrl = URL.createObjectURL(blob);
        });

        this.wave.on("play", () => {
            this.isPlaying = true;
            this.waveVisible = true;
        });

        this.wave.on("pause", () => {
            this.isPlaying = false;
        });

        this.wave.on("finish", () => {
            this.isPlaying = false;
        });

        this.wave.on("interaction", () => {
            this.wave.play();
        });

        this.wave.on("ready", () => {
            if (this.isAudioEmpty) {
                return;
            }
            this.waveIsLoaded = true;
            this.timeTotal = this.wave.getDuration();
            if (this.waveLoadAutoPlay) {
                this.wave.play();
                this.waveLoadAutoPlay = false;
            }
        });

        this.wave.on("timeupdate", () => {
            this.timeCurrent = this.wave.getCurrentTime();
            // console.log('timeupdate', this.timeCurrent);
        });

        if (this.recordEnable) {
            if (!this.url) {
                this.recordVisible = true;
            }
            RecordPlugin.getAvailableAudioDevices().then((devices) => {
                this.recordInputDevices = devices.map(device => ({
                    id: device.deviceId,
                    name: device.label || device.deviceId,
                }));
                if (!this.recordInputDeviceSelect && devices.length > 0) {
                    this.recordInputDeviceSelect = devices[0].deviceId;
                }
            });
        }
    },
    beforeDestroy() {
        if (this.wave) {
            this.wave.destroy();
        }
    },
    watch: {
        url: {
            handler(newVal) {
                if (newVal) {
                    this.waveUrl = newVal;
                    this.waveUrlSource = 'url';
                }
            },
            immediate: true,
        },
        trimUrl: {
            handler(newUrl) {
                if (newUrl) {
                    this.waveUrl = newUrl;
                    this.waveUrlSource = 'trim';
                }
            },
            immediate: true,
        },
        recordUrl: {
            handler(newUrl) {
                if (newUrl) {
                    this.waveUrl = newUrl;
                    this.waveUrlSource = 'record';
                }
            },
            immediate: true,
        },
        waveUrl: {
            handler(newVal) {
                if (this.wave && newVal) {
                    this.waveIsLoaded = false;
                    this.wave.load(newVal);
                }
            },
            immediate: true,
        },
    },
    methods: {
        doPlay() {
            if (!this.waveUrl) return;
            if (!this.waveIsLoaded) {
                this.waveLoadAutoPlay = true;
                this.wave.load(this.waveUrl);
                return;
            }
            this.wave.play();
        },
        doPause() {
            this.wave.pause();
        },
        onSliderInput(value) {
            if (value === this.timeCurrent) {
                return;
            }
            // console.log('onSliderInput', value);
            this.wave.seekTo(value / Math.max(this.timeTotal, 1));
        },
        onSeek(value) {
            // console.log('onSeek', value);
        },
        doTrimSave() {
            if (!this.isTrimming) return;
            const region = this.regions.getRegions()[0];
            const buffer = AudioUtil.audioBufferCut(
                this.wave.getDecodedData(),
                region.start,
                region.end,
            );
            this.isTrimming = false;
            this.regions.clearRegions();
            this.wave.empty();
            this.trimUrl = URL.createObjectURL(AudioUtil.audioBufferToWavBlob(buffer));
        },
        doTrim() {
            if (!this.waveUrl) return;
            let start = 1;
            let end = this.timeTotal - 1;
            if (end <= start) {
                start = 0;
                end = this.timeTotal;
            }
            this.regions.clearRegions();
            this.regions.addRegion({
                start,
                end,
                color: 'rgba(255, 255, 0, 0.1)',
                drag: true,
                resize: true,
            });
            this.isTrimming = true;
            this.waveVisible = true;
        },
        doDownload() {
            if (!this.waveUrl) return;
            const a = document.createElement("a");
            a.href = this.waveUrl;
            a.download = "audio.wav";
            a.click();
        },
        doRecord() {
            this.recordVisible = true;
        },
        async doRecordStart() {
            if (this.waveRecord.isRecording() || this.waveRecord.isPaused()) {
                this.waveRecord.stopRecording();
                return;
            }
            try {
                await this.waveRecord.startRecording({deviceId: this.recordInputDeviceSelect});
                this.isRecording = true;
                this.waveVisible = true;
            } catch (e) {
                Dialog.tipError(`${e}`);
            }
        },
        async doRecordStop() {
            if (this.waveRecord.isRecording() || this.waveRecord.isPaused()) {
                await this.waveRecord.stopRecording();
                this.isRecording = false;
            }
            this.recordVisible = false;
        },
        doRecordBack() {
            this.recordVisible = false;
        },
        doRecordClean() {
            this.recordVisible = true;
        },
        setRecordFromFile(file) {
            this.recordUrl = URL.createObjectURL(file);
            this.recordVisible = false;
        },
        getAudioBuffer() {
            return this.wave.getDecodedData();
        },
        getAudioBase64() {
            if (!this.waveUrl) return '';
            const buffer = this.getAudioBuffer();
            const data = AudioUtil.audioBufferToWav(buffer)
            return 'data:audio/wav;base64,' + Buffer.from(data).toString('base64')
        },
        onSelectFile(f) {
            this.setRecordFromFile(f);
            this.$emit('on-file-select', f)
        },
        clear(){
            this.waveUrl = null;
            this.trimUrl = null;
            this.recordUrl = null;
        }
    },
};
</script>

<style scoped>
</style>
