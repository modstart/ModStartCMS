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
    /**
     * @Util 获取当前时间戳
     * @method MS.date.timestamp
     * @return Number 时间戳
     */
    timestamp() {
        return (new Date()).getTime()
    },
    /**
     * @Util 获取当前时间戳(秒)
     * @method MS.date.timestampSecond
     * @return Number 时间戳
     */
    timestampSecond() {
        return parseInt(DateUtil.timestamp() / 1000)
    },
    /**
     * @Util 获取当前日期
     * @method MS.date.date
     * @return String 日期
     */
    date() {
        return date.format(new Date(), DateUtil.FORMAT_DATE)
    },
    /**
     * @Util 获取当前时间
     * @method MS.date.time
     * @return String 时间
     */
    time() {
        return date.format(new Date(), DateUtil.FORMAT_TIME)
    },
    /**
     * @Util 获取当前日期时间
     * @method MS.date.datetime
     * @return String 日期时间
     */
    datetime() {
        return date.format(new Date(), DateUtil.FORMAT_DATETIME)
    },
    /**
     * @Util 获取当前日期时间
     * @method MS.date.stringDatetime
     * @return String 日期时间，格式YYYYMMDD_HHmmss
     */
    stringDatetime() {
        return date.format(new Date(), 'YYYYMMDD_HHmmss')
    },
    /**
     * @Util 格式化Date
     * @method MS.date.stringDatetime
     * @param d Date 日期
     * @return String 日期时间，格式如 YYYYMMDD HHmmss
     */
    format(d, format) {
        if ('string' === typeof d) {
            d = new Date(d)
        }
        return date.format(d, format)
    },
    /**
     * @Util 格式化为日期
     * @method MS.date.formatDate
     * @param d Date 日期
     * @return String 格式化后的日期，格式 YYYY-MM-DD
     */
    formatDate(d) {
        return DateUtil.format(d, DateUtil.FORMAT_DATE)
    },
    /**
     * @Util 格式化为时间
     * @method MS.date.formatTime
     * @param d Date 日期
     * @return String 格式化后的时间，格式 HH:mm:ss
     */
    formatTime(d) {
        return DateUtil.format(d, DateUtil.FORMAT_TIME)
    },
    /**
     * @Util 格式化为时间
     * @method MS.date.formatTime
     * @param d Date 日期
     * @return String 格式化后的时间，格式 YYYY-MM-DD HH:mm:ss
     */
    formatDatetime(d) {
        return DateUtil.format(d, DateUtil.FORMAT_DATETIME)
    },
    /**
     * @Util 格式化为时间
     * @method MS.date.formatTime
     * @param timestamp Number 时间戳，单位毫秒
     * @param format String 格式化字符串
     * @return String 格式化后的时间
     */
    formatTimestamp(timestamp, format) {
        let d = new Date()
        d.setTime(timestamp)
        return DateUtil.format(d, format)
    },
    /**
     * @Util 解析日期
     * @method MS.date.parse
     * @param d String 日期
     * @param format String 格式化字符串
     * @return Date 日期
     */
    parse(d, format) {
        return date.parse(d, format)
    },
    /**
     * @Util 解析日期
     * @method MS.date.parseDate
     * @param d String 日期，格式为 YYYY-MM-DD HH:mm:ss
     * @return Date 日期
     */
    parseDatetime(d) {
        return date.parse(d, DateUtil.FORMAT_DATETIME)
    },
}
