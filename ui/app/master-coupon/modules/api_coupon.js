import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'master/coupon/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'master/coupon/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'master/coupon/del', prm)
}

export async function search_type(prm) {
    return ajaxPost(URL + 'master/coupontype/search', prm)
}

export async function search_item(prm) {
    return ajaxPost(URL + 'master/item/search_w_price', prm)
}

export async function search_packet(prm) {
    return ajaxPost(URL + 'master/packet/search_w_price', prm)
}

export async function search_expedition(prm) {
    return ajaxPost(URL + 'master/expedition/search', prm)
}

export async function search_category(prm) {
    return ajaxPost(URL + 'master/category/search', prm)
}