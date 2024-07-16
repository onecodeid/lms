import { URL, ajaxPost } from "../../assets/js/global.js"

export async function save(prm) {
    return ajaxPost(URL + 'sales/so/save', prm)
}

export async function search_expedition(prm) {
    return ajaxPost(URL + 'master/expedition/search', prm)
}

export async function search_service(prm) {
    return ajaxPost(URL + 'master/expedition/search_service', prm)
}

export async function search_payment(prm) {
    return ajaxPost(URL + 'master/paymenttype/search', prm)
}

export async function search_customer(prm) {
    return ajaxPost(URL + 'master/customer/search_autocomplete', prm)
}

export async function search_ds_customer(prm) {
    return ajaxPost(URL + 'master/customer/search_autocomplete', prm)
}

export async function search_account(prm) {
    return ajaxPost(URL + 'master/bankaccount/search', prm)
}

export async function check_coupon(prm) {
    return ajaxPost(URL + 'master/coupon/check', prm)
}