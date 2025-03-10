export const TimeUtil = {
    timestamp() {
        return Math.floor(Date.now() / 1000)
    },
    timestampMS() {
        return Date.now()
    },
    format(time, format = 'YYYY-MM-DD HH:mm:ss') {
        return dayjs(time).format(format)
    },
    formatDate(time) {
        return dayjs(time).format('YYYY-MM-DD')
    },
    dateString() {
        return dayjs().format('YYYYMMDD')
    },
    datetimeString() {
        return dayjs().format('YYYYMMDD_HHmmss')
    },
    secondsToTime(seconds) {
        seconds = parseInt(seconds.toString())
        let h = Math.floor(seconds / 3600)
        let m = Math.floor(seconds % 3600 / 60)
        let s = Math.floor(seconds % 60)
        if (h < 10) h = '0' + h
        if (m < 10) m = '0' + m
        if (s < 10) s = '0' + s
        return '00' == h ? `${m}:${s}` : `${h}:${m}:${s}`
    },
    secondsToHuman(seconds) {
        seconds = parseInt(seconds.toString())
        let h = Math.floor(seconds / 3600)
        let m = Math.floor(seconds % 3600 / 60)
        let s = Math.floor(seconds % 60)
        const result = []
        if (h > 0) result.push(`${h}${'小时'}`)
        if (m > 0) result.push(`${m}${'分钟'}`)
        if (s > 0) result.push(`${s}${'秒'}`)
        return result.join('')
    },
    replacePattern(text) {
        return text.replaceAll('{year}', dayjs().format('YYYY'))
            .replaceAll('{month}', dayjs().format('MM'))
            .replaceAll('{day}', dayjs().format('DD'))
            .replaceAll('{hour}', dayjs().format('HH'))
            .replaceAll('{minute}', dayjs().format('mm'))
            .replaceAll('{second}', dayjs().format('ss'))
    }
}
