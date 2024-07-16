import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'master/packet/search', prm)
}

export async function save(prm) {
    return ajaxPost(URL + 'master/packet/save', prm)
}

export async function del(prm) {
    return ajaxPost(URL + 'master/packet/del', prm)
}

export async function search_unit(prm) {
    return ajaxPost(URL + 'master/unit/search', prm)
}

export async function search_level_price(prm) {
    return ajaxPost(URL + 'master/price/search_by_packet', prm)
}

// DETAIL
export async function search_detail(prm) {
    return ajaxPost(URL + 'master/packetdetail/search', prm)
}

export async function search_detail_price(prm) {
    return ajaxPost(URL + 'master/packetdetail/search_price_by_item', prm)
}

export async function search_av_item(prm) {
    return ajaxPost(URL + 'master/packetdetail/search_av_item', prm)
}

export async function add_detail(prm) {
    return ajaxPost(URL + 'master/packetdetail/add', prm)
}

export async function del_detail(prm) {
    return ajaxPost(URL + 'master/packetdetail/del', prm)
}

export async function search_header_price(prm) {
    return ajaxPost(URL + 'master/packetdetail/search_header_price', prm)
}

export async function save_price(prm) {
    return ajaxPost(URL + 'master/packetdetail/save_price', prm)
}

export async function save_fee(prm) {
    return ajaxPost(URL + 'master/packetdetail/save_fee', prm)
}

// SALE
export async function search_sale(prm) {
    return ajaxPost(URL + 'master/packetsale/search', prm)
}

export async function save_promo(prm) {
    return ajaxPost(URL + 'master/packetsale/save', prm)
}