import {UUID} from "./util";
import {Cookie} from "./cookie";
import {Storage} from "./storage";

const md5 = require('md5');
const Fingerprint2 = require('fingerprintjs2')

export const FingerPrint = {
    save(name, uid) {
        uid = uid.replace(/-/g, '')
        Cookie.set(name, uid)
        Storage.set(name, uid)
        return uid
    },
    get(name, cb, prefix) {
        prefix = prefix || ''
        let uid = Cookie.get(name)
        if (!uid) {
            uid = Storage.get(name)
        }
        if (!uid) {
            uid = prefix + UUID.get()
            Fingerprint2.get(function (components) {
                uid = uid + '_' + md5(JSON.stringify(components)).substring(0, 8)
                cb(FingerPrint.save(name, uid))
            })
        } else {
            cb(FingerPrint.save(name, uid))
        }
    }
}