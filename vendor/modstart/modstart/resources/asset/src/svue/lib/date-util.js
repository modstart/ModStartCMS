let date = require('date-and-time')
if (date.default) {
    date = date.default
}
if (!date.format) {
    date = date.format
}

export const DateUtil = {
    DAY_MS: 24 * 3600 * 1000,
    MONTH_MS: 24 * 3600 * 1000 * 30,
    FORMAT_DATE: 'YYYY-MM-DD',
    FORMAT_TIME: 'HH:mm:ss',
    FORMAT_DATETIME: 'YYYY-MM-DD HH:mm:ss',
    FORMAT_TIME_HM: 'HH:mm',
    timestamp() {
        return (new Date()).getTime()
    },
    timestampSecond() {
        return parseInt(DateUtil.timestamp() / 1000)
    },
    date() {
        return date.format(new Date(), DateUtil.FORMAT_DATE)
    },
    time() {
        return date.format(new Date(), DateUtil.FORMAT_TIME)
    },
    datetime() {
        return date.format(new Date(), DateUtil.FORMAT_DATETIME)
    },
    stringDatetime() {
        return date.format(new Date(), 'YYYYMMDD_HHmmss')
    },
    format(d, format) {
        if ('string' === typeof d) {
            d = new Date(d)
        }
        return date.format(d, format)
    },
    formatDate(d) {
        return DateUtil.format(d, DateUtil.FORMAT_DATE)
    },
    formatDatetime(d) {
        return DateUtil.format(d, DateUtil.FORMAT_DATETIME)
    },
    formatTimestamp(timestamp, format) {
        let d = new Date()
        d.setTime(timestamp)
        return DateUtil.format(d, format)
    },
    parse(d, format) {
        return date.parse(d, format)
    },
    parseDatetime(d) {
        return date.parse(d, DateUtil.FORMAT_DATETIME)
    },
}
