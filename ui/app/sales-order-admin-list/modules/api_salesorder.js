import { URL, ajaxPost } from "../../assets/js/global.js"

export async function search(prm) {
    return ajaxPost(URL + 'sales/so/search', prm)
}

export async function search_item(prm) {
    return ajaxPost(URL + 'sales/sodetail/search', prm)
}

export async function search_expedition(prm) {
    return ajaxPost(URL + 'master/expedition/search', prm)
}

export async function search_service(prm) {
    return ajaxPost(URL + 'master/expedition/search_service_kecamatan', prm)
}

export async function approve(prm) {
    return ajaxPost(URL + 'sales/so/approve', prm)
}

export async function search_items(prm) {
    return ajaxPost(URL + 'master/item/search_w_price', prm)
}

export async function search_packets(prm) {
    return ajaxPost(URL + 'master/packet/search_w_price', prm)
}

export async function search_city(prm) {
    return ajaxPost(URL + 'master/city/search_full_reverse', prm)
}

export async function cancel(prm) {
    return ajaxPost(URL + 'sales/so/cancel', prm)
}

export async function save_qo(prm) {
    return ajaxPost(URL + 'sales/so/save_qo', prm)
}

export async function cancel_item(prm) {
    return ajaxPost(URL + 'sales/sodetail/cancel', prm)
}

export async function send_email(prm) {
    return ajaxPost(URL + 'sales/so/send_email', prm)
}

export async function search_payment_expanded(prm) {
    return ajaxPost(URL + 'master/paymenttype/search_expanded', prm)
}