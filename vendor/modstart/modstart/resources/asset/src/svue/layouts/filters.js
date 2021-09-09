import {DatetimeFormat} from "../lib/util";

export const FilterDate = function (d) {
    return DatetimeFormat.format(d, 'yyyy-mm-dd')
}
export const FilterTime = function (d) {
    return DatetimeFormat.format(d, 'HH:MM:ss')
}
export const FilterHourMinute = function (d) {
    return DatetimeFormat.format(d, 'HH:MM')
}