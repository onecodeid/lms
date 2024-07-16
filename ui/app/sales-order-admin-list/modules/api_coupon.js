import { URL, ajaxPost } from "../../assets/js/global.js"

export async function check_coupon(prm) {
    return ajaxPost(URL + 'master/coupon/check', prm)
}